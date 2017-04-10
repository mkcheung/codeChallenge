<?php

namespace AppBundle\Service;

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
	private $desiredMeetingTypes;
    private $originAddress;

    public function __construct(
    	JsonRPCClient $jsonRpcClient,
    	$defaultOriginAddress,
    	$meetingTypes
    ) {
    	$this->jsonRpcClient = $jsonRpcClient;
        $this->originAddress = $defaultOriginAddress;
        $this->desiredMeetingTypes = array_keys($meetingTypes);
    }

	public function getTreatmentCenters(Request $request)
	{

		$inputParameters = $request->request->all();

		if(empty($inputParameters)){
			$inputParameters = $request->query->all();
		}

		$inputParameters = $this->assembleParameters($inputParameters);

        $meetingsAssociatedWithRegion = $this->jsonRpcClient->execute('byLocals',
			[
				[
				    [
				        "state_abbr" => (empty($inputParameters['address']['state'])) ? $this->originAddress['state'] : $inputParameters['address']['state'],
				        "city" => (empty($inputParameters['address']['city'])) ? $this->originAddress['city'] : $inputParameters['address']['city']
				    ]
				]
			]
        );

        $desiredMeetings = $this->extractDesiredMeetings($meetingsAssociatedWithRegion, $inputParameters['day'], $inputParameters['meeting_type']);

		$locationCoordinates         = $this->getLocationLatitudeLongitude($inputParameters['address']);
		$meetingsDistancesCalculated = $this->calculateMeetingDistanceFromLocation($locationCoordinates, $desiredMeetings);
		$sortedMeetings              = $this->sortMeetingsFromOrigin($meetingsDistancesCalculated);

		$meetingInformation['meetings']        = $sortedMeetings;
		$meetingInformation['meetingDay']      = ucfirst((empty($inputParameters['day'])) ? 'All Day' : $inputParameters['day']); //ucfirst((empty($inputParameters['day'])) ? $this->desiredMeetingDay : $inputParameters['day']);
		$meetingInformation['meetingTypes']    = empty($inputParameters['meeting_type']) ? $this->desiredMeetingTypes : $inputParameters['meeting_type'];
		$meetingInformation['locationAddress'] = empty($inputParameters['address']) ? $this->originAddress : $inputParameters['address'];

		return $meetingInformation;
	}

	public function extractDesiredMeetings(
		array $meetings,
		$preferredMeetingDay = null,
		array $preferredMeetingTypes = []
	) {
		$desiredMeetings = [];

		$meetingDay   = $preferredMeetingDay;
		$meetingTypes = (empty($preferredMeetingTypes)) ? $this->desiredMeetingTypes : $preferredMeetingTypes;

		foreach($meetings as $meeting){

			if (in_array($meeting['meeting_type'], $meetingTypes)) {

				$meetingToBeScreened = $meeting ;
			}

			if (!empty($meetingToBeScreened) && !empty($meetingDay) && $meetingDay != $meetingToBeScreened['time']['day']) {
				continue ;
			}

			if (!empty($meetingToBeScreened)) {
				$desiredMeetings[] = $meetingToBeScreened;
				unset($meetingToBeScreened);
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
    	$meetings
    ) {

		foreach ($meetings as &$meeting) {

	        $delta_lat = ($locationCoordinates['lat']) - $meeting['address']['lat'];
	        $delta_lon = ($locationCoordinates['lng']) - $meeting['address']['lng'];
        	$distance  = sin(deg2rad($meeting['address']['lat'])) * sin(deg2rad($locationCoordinates['lat'])) + cos(deg2rad($meeting['address']['lat'])) * cos(deg2rad($locationCoordinates['lat'])) * cos(deg2rad($delta_lon)) ;
	        $distance  = acos($distance);
	        $distance  = rad2deg($distance);
	        $distance  = $distance * static::NAUTICAL_MILES_PER_LAT * static::MILES_PER_NAUT_MILE;
	        $distance  = round($distance, 6);
	        $meeting['distanceFromOrigin'] = $distance;
		}

		return $meetings;
    }

    public function sortMeetingsFromOrigin($meetingsWithDistancesCalculated) {
    	usort($meetingsWithDistancesCalculated, [$this,'sortByDistance']);
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

		$parameters = [];

		foreach ($incomingParameters as $key => $value) {
			switch($key){
				case 'street_address':
					$parameters['address']['street_address'] = $value;
					break;
				case 'city':
					$parameters['address']['city'] = $value;
					break;
				case 'state':
					$parameters['address']['state'] = $value;
					break;
				case 'zip_code':
					$parameters['address']['zip_code'] = $value;
					break;
				case 'meeting_type':
					$parameters['meeting_type'] = $value;
					break;
				case 'day':
					$parameters['day'] = $value;
					break;
				default:
					break;
			}
		}

		if (!(array_key_exists('meeting_type', $parameters))) {
			$parameters['meeting_type'] = [];
		}

		if (
			empty($parameters['address']['street_address']) ||
			empty($parameters['address']['city']) ||
			empty($parameters['address']['state']) ||
			empty($parameters['address']['zip_code'])
		) {
			$parameters['address'] = null;
		}

		return $parameters;
	}
}