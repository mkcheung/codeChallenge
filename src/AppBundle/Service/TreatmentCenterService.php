<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityRepository;
use JsonRPC\Client as JsonRPCClient;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client as GuzzleClient;

class TreatmentCenterService
{

    const NAUTICAL_MILES_PER_LAT = 60;
    const MILES_PER_NAUT_MILE    = 1.1515;

    private $jsonRpcClient;

    private $meetingTypeRepository;

    private $streetAddress;
    private $city;
    private $state;
    private $zipCode;
    private $dayOfMeetingRequested;
    private $meetingTypesRequested = [];

    public function __construct(
        JsonRPCClient $jsonRpcClient,
        EntityRepository $meetingTypeRepository,
        $defaultStreetAddress,
        $defaultCity,
        $defaultState,
        $defaultZipCode
    ) {

        $this->jsonRpcClient         = $jsonRpcClient;
        $this->meetingTypeRepository = $meetingTypeRepository;
        $this->streetAddress         = $defaultStreetAddress;
        $this->city                  = $defaultCity;
        $this->state                 = $defaultState;
        $this->zipCode               = $defaultZipCode;

        $defaultMeetingTypes = $this->meetingTypeRepository->findAll();
        foreach ($defaultMeetingTypes as $defaultMeetingType) {
            $this->meetingTypesRequested[] = $defaultMeetingType->getMeetingTypeInitials();
        }
    }

    public function getTreatmentCenters(Request $request)
    {

        $inputParameters = $request->request->all();

        if (empty($inputParameters)) {
            $inputParameters = $request->query->all();
        }

        $this->assembleParameters($inputParameters);

        $meetingsAssociatedWithRegion = $this->jsonRpcClient->execute('byLocals',
            [
                [
                    [
                        "state_abbr" => $this->state,
                        "city"       => $this->city,
                    ],
                ],
            ]
        );

        $desiredMeetings = $this->extractDesiredMeetings($meetingsAssociatedWithRegion);

        $locationCoordinates         = $this->getLocationLatitudeLongitude();
        $meetingsDistancesCalculated = $this->calculateMeetingDistanceFromLocation($locationCoordinates,
            $desiredMeetings);
        $sortedMeetings              = $this->sortMeetingsFromOrigin($meetingsDistancesCalculated);

        $meetingInformation['meetings']        = $sortedMeetings;
        $meetingInformation['meetingDay']      = ucfirst((empty($this->dayOfMeetingRequested)) ? 'All Day' : $this->dayOfMeetingRequested);
        $meetingInformation['meetingTypes']    = $this->meetingTypesRequested;
        $meetingInformation['locationAddress'] = [
            'street_address' => $this->streetAddress,
            'city'           => $this->city,
            'state'          => $this->state,
            'zip_code'       => $this->zipCode,
        ];

        return $meetingInformation;
    }

    public function extractDesiredMeetings(array $meetings)
    {
        $desiredMeetings = [];

        foreach ($meetings as $meeting) {

            if (in_array($meeting['meeting_type'], $this->meetingTypesRequested)) {

                $meetingToBeScreened = $meeting;
            }

            if ( !empty($meetingToBeScreened) && !empty($this->dayOfMeetingRequested) && $this->dayOfMeetingRequested != $meetingToBeScreened['time']['day']) {
                continue;
            }

            if ( !empty($meetingToBeScreened)) {
                $desiredMeetings[] = $meetingToBeScreened;
                unset($meetingToBeScreened);
            }

        }

        return $desiredMeetings;
    }

    public function getLocationLatitudeLongitude()
    {

        $googleGeolocationUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($this->streetAddress) . ',' . urlencode($this->city) . ',' . urlencode($this->state);

        $client       = new GuzzleClient();
        $res          = $client->request('GET', $googleGeolocationUrl);
        $jsonResponse = $res->getBody();

        $geocodeResponse    = json_decode($jsonResponse);
        $geoCodeResults     = array_shift($geocodeResponse->results);
        $addressCoordinates = $geoCodeResults->geometry->location;

        return (array)$addressCoordinates;
    }

    public function calculateMeetingDistanceFromLocation(
        array $locationCoordinates,
        $meetings
    ) {

        foreach ($meetings as &$meeting) {

            $delta_lat                     = ($locationCoordinates['lat']) - $meeting['address']['lat'];
            $delta_lon                     = ($locationCoordinates['lng']) - $meeting['address']['lng'];
            $distance                      = sin(deg2rad($meeting['address']['lat'])) * sin(deg2rad($locationCoordinates['lat'])) + cos(deg2rad($meeting['address']['lat'])) * cos(deg2rad($locationCoordinates['lat'])) * cos(deg2rad($delta_lon));
            $distance                      = acos($distance);
            $distance                      = rad2deg($distance);
            $distance                      = $distance * static::NAUTICAL_MILES_PER_LAT * static::MILES_PER_NAUT_MILE;
            $distance                      = round($distance, 6);
            $meeting['distanceFromOrigin'] = $distance;
        }

        return $meetings;
    }

    public function sortMeetingsFromOrigin($meetingsWithDistancesCalculated)
    {
        usort($meetingsWithDistancesCalculated, [$this, 'sortByDistance']);

        return $meetingsWithDistancesCalculated;
    }

    /**
     * @codeCoverageIgnore
     */
    private static function sortByDistance($a, $b)
    {
        if ($a['distanceFromOrigin'] == $b['distanceFromOrigin']) {
            return 0;
        }

        return ($a['distanceFromOrigin'] < $b['distanceFromOrigin']) ? -1 : 1;
    }

    /**
     * @codeCoverageIgnore
     */
    private function assembleParameters($incomingParameters)
    {

        $this->dayOfMeetingRequested = $incomingParameters['day'];

        if ((array_key_exists('meeting_type', $incomingParameters))) {
            $this->meetingTypesRequested = $incomingParameters['meeting_type'];
        }

        if (
            empty($incomingParameters['street_address']) ||
            empty($incomingParameters['city']) ||
            empty($incomingParameters['state']) ||
            empty($incomingParameters['zip_code'])
        ) {

            return;
        }

        $this->streetAddress = $incomingParameters['street_address'];
        $this->city          = $incomingParameters['city'];
        $this->state         = $incomingParameters['state'];
        $this->zipCode       = $incomingParameters['zip_code'];

        return;
    }

    public function assembleCacheKey(Request $request){

        $inputParameters = $request->request->all();

        if (empty($inputParameters)) {
            $inputParameters = $request->query->all();
        }
        $cacheKey = '';

        $cacheKey .= $inputParameters['street_address'];
        $cacheKey .= $inputParameters['city'];
        $cacheKey .= $inputParameters['state'];
        $cacheKey .= $inputParameters['zip_code'];
        $cacheKey .= $inputParameters['day'];

        if (!empty($inputParameters['meeting_type'])) {
            $meetingTypes = $inputParameters['meeting_type'];
            foreach ($meetingTypes as $meetingType) {
                $cacheKey .= $meetingType;
            }
        }

        if(empty($cacheKey)){
            return 'default';
        }

        return $cacheKey;
    }
}