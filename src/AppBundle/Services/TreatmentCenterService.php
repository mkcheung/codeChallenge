<?php

namespace AppBundle\Services;

use JsonRPC\Client as JsonRPCClient;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client as GuzzleClient;

class TreatmentCenterService
{

	const NAUTICAL_MILES_PER_LAT = 60;
	const MILES_PER_NAUT_MILE = 1.1515;

	private $jsonRpcClient;

	private $meetingsWithinRegion;

	private $desiredMeetingDay = 'monday';
	private $desiredMeetingTypes = ['AA', 'NA'];

    private $originAddress = [
    	'street_address' => '517 4th Ave.',
    	'city' => 'San Diego',
    	'state' => 'CA',
    	'zip_code' => '92101'
    ];

    public function __construct(
    	JsonRPCClient $jsonRpcClient,
    	$userId,
    	$userPassword
    ) {
    	$this->jsonRpcClient = $jsonRpcClient;
        $this->jsonRpcClient->authentication($userId, $userPassword);
    }

	public function getTreatmentCenters(Request $request)
	{

		$inputParameters = $request->request->all();
		if(empty($inputParameters)){
			$inputParameters = $request->query->all();
		}
		$this->assembleParameters($inputParameters);

        $this->meetingsWithinRegion = $this->jsonRpcClient->execute('byLocals',
			[
				[
				    [
				        "state_abbr" => $this->originAddress['state'],
				        "city" => $this->originAddress['city']
				    ]
				]
			]
        );

        $desiredMeetings = $this->extractDesiredMeetings();

		$locationCoordinates         = $this->getLocationLatitudeLongitude();
		$meetingsDistancesCalculated = $this->calculateMeetingDistanceFromLocation($locationCoordinates);
		$sortedMeetings              = $this->sortMeetingsFromOrigin($meetingsDistancesCalculated);

		$meetingInformation['meetings']        = $sortedMeetings;
		$meetingInformation['meetingDay']      = ucfirst($this->desiredMeetingDay);
		$meetingInformation['meetingTypes']    = $this->desiredMeetingTypes;
		$meetingInformation['locationAddress'] = $this->originAddress;

		return $meetingInformation;
	}

	public function extractDesiredMeetings(
		array $setOfMeetings = null,
		$preferredMeetingDay = null,
		array $preferredMeetingTypes = null
	) {
		$desiredMeetings = [];

		$meetings     = (empty($setOfMeetings)) ? $this->meetingsWithinRegion : $setOfMeetings;
		$meetingDay   = (empty($preferredMeetingDay)) ? $this->desiredMeetingDay : $preferredMeetingDay;
		$meetingTypes = (empty($preferredMeetingTypes)) ? $this->desiredMeetingTypes : $preferredMeetingTypes;

		foreach($meetings as $meeting){

			if (($meeting['time']['day'] == $meetingDay) && in_array($meeting['meeting_type'], $meetingTypes)){
				$desiredMeetings[] = $meeting;
			}
		}

		return $desiredMeetings;
	}

	public function getLocationLatitudeLongitude(array $address = null)
	{

		$streetAddress = (empty($address['street_address'])) ? $this->originAddress['street_address'] : $address['street_address'];
		$city = (empty($address['city'])) ? $this->originAddress['city'] : $address['city'];
		$state = (empty($address['state'])) ? $this->originAddress['state'] : $address['state'];

		$googleGeolocationUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($streetAddress).','.urlencode($city).','.urlencode($state);

		$client = new GuzzleClient();
		$res = $client->request('GET', $googleGeolocationUrl);
		$jsonResponse = $res->getBody();

        $geocodeResponse = json_decode($jsonResponse);
        $geoCodeResults = array_shift($geocodeResponse->results);
        $addressCoordinates = $geoCodeResults->geometry->location;
        return (array) $addressCoordinates;
	}

    public function calculateMeetingDistanceFromLocation(
    	array $locationCoordinates,
    	$meetings = null
    ) {

		$utilizedMeetings = (empty($meetings)) ? $this->meetingsWithinRegion : $meetings;

		$meetingDistancesFromOrigin = [];

		foreach ($utilizedMeetings as &$meeting) {

	        $delta_lat = ($locationCoordinates['lat']) - $meeting['address']['lat'];
	        $delta_lon = ($locationCoordinates['lng']) - $meeting['address']['lng'];
        	$distance  = sin(deg2rad($meeting['address']['lat'])) * sin(deg2rad($locationCoordinates['lat'])) + cos(deg2rad($meeting['address']['lat'])) * cos(deg2rad($locationCoordinates['lat'])) * cos(deg2rad($delta_lon)) ;
	        $distance  = acos($distance);
	        $distance  = rad2deg($distance);
	        $distance  = $distance * static::NAUTICAL_MILES_PER_LAT * static::MILES_PER_NAUT_MILE;
	        $distance  = round($distance, 6);
	        $meeting['distanceFromOrigin'] = $distance;
		}
		return $utilizedMeetings;
    }

    public function sortMeetingsFromOrigin($meetingsWithDistancesCalculated) {
    	usort($meetingsWithDistancesCalculated, [$this,'sortByDistance']);
    	return $meetingsWithDistancesCalculated;
    }

	private static function sortByDistance($a, $b)
	{
		if ($a['distanceFromOrigin'] == $b['distanceFromOrigin']) {
			return 0;
		}
		return ($a['distanceFromOrigin'] < $b['distanceFromOrigin']) ? -1 : 1;
	}

	private function assembleParameters($incomingParameters)
	{
		foreach ($incomingParameters as $key => $value) {
			switch($key){
				case 'street_address':
					$this->originAddress['street_address'] = $value;
					break;
				case 'city':
					$this->originAddress['city'] = $value;
					break;
				case 'state':
					$this->originAddress['state'] = $value;
					break;
				case 'zip_code':
					$this->originAddress['zip_code'] = $value;
					break;
				case 'meeting_type':
					$this->desiredMeetingTypes = $value;
					break;
				case 'day':
					$this->desiredMeetingDay = $value;
					break;
			}
		}
	}
}