<?php

namespace AppBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Service as TreatmentCenterService;

use JsonRPC\Client as JsonRPCClient;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;

class TreatmentCenterServiceTest extends WebTestCase
{

	protected $originAddress = [
		"address" => [
			"street_address" => "517 4th Ave.",
			"city" => "San Diego",
			"state" => "CA",
			"zip_code" => 92101
	    ]
	];

	protected $meetingTypes = [
		"AA" => "Alcoholics Anonymous",
		"MA" => "Marijuana Anonymous",
		"OA" => "Overeaters Anonymous",
		"NA" => "Alcoholics Anonymous"
	];

	protected $meetings = [
		[
		    "id" => 29555,
		    "time_id" => 15430,
		    "address_id" => 17832,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker",
		    "meeting_type" => "AA",
		    "meeting_name" => "Saturday Night Live",
		    "language" => "English",
		    "raw_address" => "San jose, 1388 S Bascom Ave San Jose CA, San Jose, , CA",
		    "location" => "San jose",
		    "address" => [
		      "id" => 17832,
		      "street" => "",
		      "zip" => "95112",
		      "city" => "San Jose",
		      "state_abbr" => "CA",
		      "lat" => "37.370040",
		      "lng" =>  "-121.892093"
		    ],
		    "time" => [
		      "id" => 15430,
		      "day" => "saturday",
		      "hour" => 0
		    ]
	   ],
		[
		    "id" => 29557,
		    "time_id" => 18430,
		    "address_id" => 17132,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker",
		    "meeting_type" => "AA",
		    "meeting_name" => "Saturday Night Live 2",
		    "language" => "English",
		    "raw_address" => "San jose, 1118 S Bascom Ave San Jose CA, San Jose, , CA",
		    "location" => "San jose",
		    "address" => [
		      "id" => 17832,
		      "street" => "",
		      "zip" => "95112",
		      "city" => "San Jose",
		      "state_abbr" => "CA",
		      "lat" => "37.370040",
		      "lng" =>  "-121.892093"
		    ],
		    "time" => [
		      "id" => 16000,
		      "day" => "saturday",
		      "hour" => 0
		    ]
	   ],
	   [
		    "id" => 30176,
		    "time_id" => 15523,
		    "address_id" => 17832,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker , Participation",
		    "meeting_type" => "NA",
		    "meeting_name" => "Newcomer Introduction To AA",
		    "language" => ".",
		    "raw_address" =>"alono club of san jose, 1122 Fair Ave, san_jose, , CA",
		    "location" => "alono club of san jose",
		    "address" => [
		      "id" => 17832,
		      "street" => "",
		      "zip" => "95112",
		      "city" => "San Jose",
		      "state_abbr" => "CA",
		      "lat" => "37.370040",
		      "lng" =>  "-121.892093"
		    ],
		    "time" => [
		      "id" => 15523,
		      "day" => "thursday",
		      "hour" => 1800
		    ]
		]
	];

	protected $JsonRPCClient;
	protected $treatmentCenterService;


    public function setUp()
    {
    	$this->JsonRPCClient = new JsonRPCClient('http://tools.referralsolutionsgroup.com/meetings-api/v1/');
    	$this->treatmentCenterService = new TreatmentCenterService\TreatmentCenterService($this->JsonRPCClient, 'oXO8YKJUL2X3oqSpFpZ5', 'JaiXo2lZRJVn5P4sw0bt', $this->originAddress, $this->meetingTypes);
    }

    public function testExtractDesiredMeetingsByDay()
    {
    	$extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings, 'saturday');
    	$this->assertEquals(2, count($extractedMeetings));

    	foreach($extractedMeetings as $extractedMeeting){
    		$this->assertEquals('saturday', $extractedMeeting['time']['day']);
    	}

    }

    public function testExtractDesiredMeetingsByType()
    {
    	$extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings, null, ['NA'] );
    	$this->assertEquals(1, count($extractedMeetings));

    	foreach($extractedMeetings as $extractedMeeting){
    		$this->assertEquals('NA', $extractedMeeting['meeting_type']);
    	}

    }
}
