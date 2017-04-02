<?php

namespace AppBundle\Services;

use JsonRPC\Client;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;

class TreatmentCenter
{

	private $jsonRpcClient;

	private $desiredMeetingDay = 'monday';
	private $desiredMeetingTypes = ['AA', 'NA'];
	private $epicenterParams = [
            [
                [
                    "state_abbr" => "CA",
                    "city" => "San Diego"
                ]
            ]
        ];

    private $originAddress = [
    	'street_address' => '517 4th Ave.',
    	'city' => 'San Diego',
    	'state' => 'CA',
    	'zip_code' => '92101'
    ];

    public function __construct($treatmentCenterUrl, $userId, $userPassword)
    {

    	$this->jsonRpcClient = new Client($treatmentCenterUrl);
        $this->jsonRpcClient->authentication($userId, $userPassword);
    }

	public function getTreatmentCenters()
	{

        $meetingsWithRegion = $this->jsonRpcClient->execute('byLocals', $this->epicenterParams);
        $meetingsDesired = $this->extractDesiredMeetings($meetingsWithRegion);
		$originCoordinates = $this->getOriginLatitudeLongitude();
		$this->calculateMeetingDistanceFromOrigin($meetingsDesired, $originCoordinates);
		$this->sortMeetingsFromOrigin($meetingsDesired);

		return $meetingsDesired;
	}

	private function extractDesiredMeetings($meetingsWithRegion)
	{

		$meetingsOnDesiredDay = [];

		foreach($meetingsWithRegion as $meeting){
			if(($meeting['time']['day'] = $this->desiredMeetingDay) && in_array($meeting['meeting_type'], $this->desiredMeetingTypes)){
				$meetingsOnDesiredDay[] = $meeting;
			}
		}

		return $meetingsOnDesiredDay;
	}

	private function getOriginLatitudeLongitude()
	{

		$googleGeolocationUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($this->originAddress['street_address']).','.urlencode($this->originAddress['city']).','.urlencode($this->originAddress['state']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $googleGeolocationUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $jsonResponse = curl_exec($ch);
        $geocodeResponse = json_decode($jsonResponse);
        $geoCodeResults = array_shift($geocodeResponse->results);
        $originCoordinates = $geoCodeResults->geometry->location;
        return $originCoordinates;
	}

    private function calculateMeetingDistanceFromOrigin(&$meetingsDesired, $originCoordinates)
    {

		$meetingDistancesFromOrigin = [];

		foreach ($meetingsDesired as &$meeting) {

	        $delta_lat = ($originCoordinates->lat) - $meeting['address']['lat'];
	        $delta_lon = ($originCoordinates->lng) - $meeting['address']['lng'];
        	$distance  = sin(deg2rad($meeting['address']['lat'])) * sin(deg2rad($originCoordinates->lat)) + cos(deg2rad($meeting['address']['lat'])) * cos(deg2rad($originCoordinates->lat)) * cos(deg2rad($delta_lon)) ;
	        $distance  = acos($distance);
	        $distance  = rad2deg($distance);
	        $distance  = $distance * 60 * 1.1515;
	        $distance  = round($distance, 6);
	        $meeting['distanceFromOrigin'] = $distance;
		}

    }

	private static function sortByDistance($a, $b)
	{
		if ($a['distanceFromOrigin'] == $b['distanceFromOrigin']) {
			return 0;
		}
		return ($a['distanceFromOrigin'] < $b['distanceFromOrigin']) ? -1 : 1;
	}

    private function sortMeetingsFromOrigin(&$meetingsDesired) {

    	usort($meetingsDesired, [$this,'sortByDistance']);
    }

}