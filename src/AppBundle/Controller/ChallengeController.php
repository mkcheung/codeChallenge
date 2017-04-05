<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChallengeController extends Controller {

    protected $states = [
        "AL" => "Alabama",
        "AK" => "Alaska",
        "AZ" => "Arizona",
        "AR" => "Arkansas",
        "CA" => "California",
        "CO" => "Colorado",
        "CT" => "Connecticut",
        "DE" => "Delaware",
        "FL" => "Florida",
        "GA" => "Georgia",
        "HI" => "Hawaii",
        "ID" => "Idaho",
        "IL" => "Illinois",
        "IN" => "Indiana",
        "IA" => "Iowa",
        "KS" => "Kansas",
        "KY" => "Kentucky",
        "LA" => "Louisiana",
        "ME" => "Maine",
        "MD" => "Maryland",
        "MA" => "Massachusetts",
        "MI" => "Michigan",
        "MN" => "Minnesota",
        "MS" => "Mississippi",
        "MO" => "Missouri",
        "MT" => "Montana",
        "NE" => "Nebraska",
        "NV" => "Nevada",
        "NH" => "New Hampshire",
        "NJ" => "New Jersey",
        "NM" => "New Mexico",
        "NY" => "New York",
        "NC" => "North Carolina",
        "ND" => "North Dakota",
        "OH" => "Ohio",
        "OK" => "Oklahoma",
        "OR" => "Oregon",
        "PA" => "Pennsylvania",
        "RI" => "Rhode Island",
        "SC" => "South Carolina",
        "SD" => "South Dakota",
        "TN" => "Tennessee",
        "TX" => "Texas",
        "UT" => "Utah",
        "VT" => "Vermont",
        "VA" => "Virginia",
        "WA" => "Washington",
        "WV" => "West Virginia",
        "WI" => "Wisconsin",
        "WY" => "Wyoming",
        "AS" => "American Samoa",
        "DC" => "District Of Columbia",
        "FM" => "Federated States Of Micronesia",
        "GU" => "Guam",
        "MH" => "Marshall Islands",
        "MP" => "Northern Mariana Islands",
        "PW" => "Palau",
        "PR" => "Puerto Rico",
        "VI" => "Virgin Islands",
        "AE" => "Armed Forces Africa",
        "AA" => "Armed Forces Americas",
        "AE" => "Armed Forces Canada",
        "AE" => "Armed Forces Europe",
        "AE" => "Armed Forces Middle East",
        "AP" => "Armed Forces Pacific"
    ];

    protected $days = [
        "sunday" => "Sunday",
        "monday" => "Monday",
        "tuesday" => "Tuesday",
        "wednesday" => "Wednesday",
        "thursday" => "Thursday",
        "friday" => "Friday",
        "saturday" => "Saturday"
    ];

    protected $meetingTypes = [
        "AA" => "Alcoholics Anonymous",
        "MA" => "Marijuana Anonymous",
        "OA" => "Overeaters Anonymous",
        "NA" => "Narcotics Anonymous"
    ];

	public function printr($bug)
	{
		echo '<pre>';
		var_dump(__FILE__);
		var_dump(__LINE__);
		var_dump($bug);
		die;
	}

	/**
	* @Route("/challenge/")
	*/
	public function indexAction()
	{
        return $this->render('default/address_input.html.twig', array(
            'page_title' => 'Request Meeting Times and Locations',
            'states' => $this->states,
            'days' => $this->days,
            'meeting_types' => $this->meetingTypes
        ));
	}
	/**
	* @Route("/challenge/meetingsFromOrigin")
	*/
	public function meetingsFromOriginAction(Request $request)
	{
        $treatmentCenter = $this->get('app.treatment_center_service');
        $results = $treatmentCenter->getTreatmentCenters($request);
        return $this->render('default/meeting.html.twig', array(
            'page_title' => 'Monday Treatment Meetings From RB',
            'results' => $results,
        ));
	}
}