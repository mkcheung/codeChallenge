<?php

namespace AppBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;
use AppBundle\Service as TreatmentCenterService;

use JsonRPC\Client as JsonRPCClient;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;
use Symfony\Component\HttpFoundation\Request;

class TreatmentCenterServiceTest extends WebTestCase
{

	protected $originAddress = [
		"street_address" => "517 4th Ave.",
		"city" => "San Diego",
		"state" => "CA",
		"zip_code" => 92101
	];

	protected $manualAddress = [
		"street_address" => "4657 Cranbrook Ct.",
		"city" => "La Jolla",
		"state" => "CA",
		"zip_code" => 92037
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
		],
		[
			"id" => 91968,
			"time_id" => 15419,
			"address_id" => 37165,
			"type" =>  "MeetingItem",
			"details" =>  "Format: Speaker, Discussion/Participation",
			"meeting_type" => "NA",
			"meeting_name" =>  "Queen Of Apostles Church",
			"language" => "English",
			"raw_address" =>  "Queen of Apostles Church, 4911 Moorpark Avenue, San Jose, , CA",
			"location" =>  "Queen of Apostles Church",
			"address" => [
			  "id" => 37165,
			  "street" =>  "4911 Moorpark Ave",
			  "zip" => "95129",
			  "city" => "San Jose",
			  "state_abbr" => "CA",
			  "lat" => "37.313952",
			  "lng" =>  "-121.99026510345"
			],
			"time" => [
			  "id" => 15419,
			  "day" => "saturday",
			  "hour" => 2000,
			]
		],
		[
			"id" => 92004,
			"time_id" => 15437,
			"address_id" => 17832,
			"type" =>  "MeetingItem",
			"details" => "Format:",
			"meeting_type" => "NA",
			"meeting_name" =>  "San Thomas Park Recreation Center",
			"language" => ".",
			"raw_address" =>  "San Thomas Park Recreation Center, 4093 Valerie Drive, San Jose, , CA",
			"location" =>  "San Thomas Park Recreation Center",
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
			  "id" => 15437,
			  "day" => "wednesday",
			  "hour" => 2000
			]
		],
		[
			"id" => 92005,
			"time_id" => 15478,
			"address_id" => 17832,
			"type" =>  "MeetingItem",
			"details" => "Format:",
			"meeting_type" => "NA",
			"meeting_name" =>  "San Thomas Park Recreation Center",
			"language" => ".",
			"raw_address" =>  "San Thomas Park Recreation Center, 4093 Valerie Drive, San Jose, , CA",
			"location" =>  "San Thomas Park Recreation Center",
			"address" => [
			  "id" => 17832,
			  "street" => "",
			  "zip" => "95112",
			  "city" => "San Jose",
			  "state_abbr" => "CA",
			  "lat" => "37.370040",
			  "lng" => "-121.892093"
			],
			"time" => [
			  "id" => 15478,
			  "day" => "tuesday",
			  "hour" => 2000
			]
		]
	];


	protected $meetingsForDistances = [
		[
		    "id" => 30201,
		    "time_id" => 15419,
		    "address_id" => 17935,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker Meeting",
		    "meeting_type" => "AA",
		    "meeting_name" => "Second Tradition Group Speaker Meeting",
		    "language" => "English",
		    "raw_address" => "Lincoln Glen Church, 2700 Booksin Ave, San Jose, , CA",
		    "location" => "Lincoln Glen Church",
		    "address" => [
		      "id" => 17935,
		      "street" => "2700 Booksin Ave",
		      "zip" => "95125",
		      "city" => "San Jose",
		      "state_abbr" => "CA",
		      "lat" => "37.275002",
		      "lng" =>  "-121.897159"
		    ],
		    "time" => [
		      "id" => 15419,
		      "day" => "saturday",
		      "hour" => 2000
		    ]
	   ],
		[
		    "id" => 29555,
		    "time_id" => 15430,
		    "address_id" => 17832,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker",
		    "meeting_type" => "AA",
		    "meeting_name" => "Saturday Night Live",
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
		      "id" => 15430,
		      "day" => "saturday",
		      "hour" => 0
		    ]
	   ]
	];

	protected $meetingsToBeSortedByDistance = [
		[
		    "id" => 29555,
		    "time_id" => 15430,
		    "address_id" => 17832,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker",
		    "meeting_type" => "AA",
		    "meeting_name" => "Saturday Night Live",
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
		      "id" => 15430,
		      "day" => "saturday",
		      "hour" => 0
		    ],
		    "distanceFromOrigin" => 6.566884
	   ],
		[
		    "id" => 30201,
		    "time_id" => 15419,
		    "address_id" => 17935,
		    "type" => "MeetingItem",
		    "details" => "Format: Speaker Meeting",
		    "meeting_type" => "AA",
		    "meeting_name" => "Second Tradition Group Speaker Meeting",
		    "language" => "English",
		    "raw_address" => "Lincoln Glen Church, 2700 Booksin Ave, San Jose, , CA",
		    "location" => "Lincoln Glen Church",
		    "address" => [
		      "id" => 17935,
		      "street" => "2700 Booksin Ave",
		      "zip" => "95125",
		      "city" => "San Jose",
		      "state_abbr" => "CA",
		      "lat" => "37.275002",
		      "lng" =>  "-121.897159"
		    ],
		    "time" => [
		      "id" => 15419,
		      "day" => "saturday",
		      "hour" => 2000
		    ],
		    "distanceFromOrigin" => 6.014824
	   ]
	];

	protected $JsonRPCClient;
	protected $treatmentCenterService;

    public function setUp()
    {
    	$this->JsonRPCClient = new JsonRPCClient('http://tools.referralsolutionsgroup.com/meetings-api/v1/');
    	$this->treatmentCenterService = new TreatmentCenterService\TreatmentCenterService($this->JsonRPCClient, 'oXO8YKJUL2X3oqSpFpZ5', 'JaiXo2lZRJVn5P4sw0bt', $this->originAddress, $this->meetingTypes);
    }

    public function testGetTreatmentCentersDefaults()
    {

    	$sampleRequest = new Request();
    	$sampleRequest->query->set("street_address", '');
    	$sampleRequest->query->set("city", '');
    	$sampleRequest->query->set("state", '');
    	$sampleRequest->query->set("zip_code", null);
    	$sampleRequest->query->set("meeting_type", []);
    	$sampleRequest->query->set("day", '');

    	$treatmentCenterMeetings = $this->treatmentCenterService->getTreatmentCenters($sampleRequest);
    	$meetings = $treatmentCenterMeetings['meetings'];
    	$this->assertEquals('All Day', $treatmentCenterMeetings['meetingDay']);
    	$this->assertEquals(['AA', 'MA', 'OA', 'NA'], $treatmentCenterMeetings['meetingTypes']);
    	$this->assertEquals("517 4th Ave.", $treatmentCenterMeetings['locationAddress']['street_address']);
    	$this->assertEquals("San Diego", $treatmentCenterMeetings['locationAddress']['city']);
    	$this->assertEquals("CA", $treatmentCenterMeetings['locationAddress']['state']);
    	$this->assertEquals(92101, $treatmentCenterMeetings['locationAddress']['zip_code']);
    	$this->assertEquals(320, count($meetings));
    }

    public function testGetTreatmentCentersUsingGet()
    {
    	$sampleRequest = new Request();
    	$sampleRequest->query->set("street_address", "502 South Park Dr.");
    	$sampleRequest->query->set("city", "San Jose");
    	$sampleRequest->query->set("state", "CA");
    	$sampleRequest->query->set("zip_code", 95129);
    	$sampleRequest->query->set("meeting_type", ['AA']);
    	$sampleRequest->query->set("day", 'monday');

    	$treatmentCenterMeetings = $this->treatmentCenterService->getTreatmentCenters($sampleRequest);
    	$meetings = $treatmentCenterMeetings['meetings'];
    	$this->assertEquals('Monday', $treatmentCenterMeetings['meetingDay']);
    	$this->assertEquals(['AA'], $treatmentCenterMeetings['meetingTypes']);
    	$this->assertEquals("502 South Park Dr.", $treatmentCenterMeetings['locationAddress']['street_address']);
    	$this->assertEquals("San Jose", $treatmentCenterMeetings['locationAddress']['city']);
    	$this->assertEquals("CA", $treatmentCenterMeetings['locationAddress']['state']);
    	$this->assertEquals(95129, $treatmentCenterMeetings['locationAddress']['zip_code']);
    	$this->assertEquals(3, count($meetings));

    	foreach ($meetings as $meeting) {
    		$this->assertEquals('monday', $meeting['time']['day']);
    		$this->assertEquals('AA', $meeting['meeting_type']);
    	}

    }

    public function testGetTreatmentCentersUsingPost()
    {
    	$sampleRequest = new Request();
    	$sampleRequest->request->set("street_address", "502 South Park Dr.");
    	$sampleRequest->request->set("city", "San Jose");
    	$sampleRequest->request->set("state", "CA");
    	$sampleRequest->request->set("zip_code", 95129);
    	$sampleRequest->request->set("meeting_type", ['AA']);
    	$sampleRequest->request->set("day", 'monday');

    	$treatmentCenterMeetings = $this->treatmentCenterService->getTreatmentCenters($sampleRequest);
    	$meetings = $treatmentCenterMeetings['meetings'];
    	$this->assertEquals('Monday', $treatmentCenterMeetings['meetingDay']);
    	$this->assertEquals(['AA'], $treatmentCenterMeetings['meetingTypes']);
    	$this->assertEquals("502 South Park Dr.", $treatmentCenterMeetings['locationAddress']['street_address']);
    	$this->assertEquals("San Jose", $treatmentCenterMeetings['locationAddress']['city']);
    	$this->assertEquals("CA", $treatmentCenterMeetings['locationAddress']['state']);
    	$this->assertEquals(95129, $treatmentCenterMeetings['locationAddress']['zip_code']);
    	$this->assertEquals(3, count($meetings));

    	foreach ($meetings as $meeting) {
    		$this->assertEquals('monday', $meeting['time']['day']);
    		$this->assertEquals('AA', $meeting['meeting_type']);
    	}

    }

    public function testExtractDesiredMeetingsDefault()
    {
    	$extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings);
    	$this->assertEquals(6, count($extractedMeetings));

    }

    public function testExtractDesiredMeetingsByDay()
    {
    	$extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings, 'saturday');
    	$this->assertEquals(3, count($extractedMeetings));

    	foreach($extractedMeetings as $extractedMeeting){
    		$this->assertEquals('saturday', $extractedMeeting['time']['day']);
    	}

    }

    public function testExtractDesiredMeetingsByType()
    {
    	$extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings, null, ['NA'] );
    	$this->assertEquals(4, count($extractedMeetings));

    	foreach($extractedMeetings as $extractedMeeting){
    		$this->assertEquals('NA', $extractedMeeting['meeting_type']);
    	}

    }

    public function testExtractDesiredMeetingsByDayAndType()
    {
    	$extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings, 'saturday', ['NA'] );
    	$this->assertEquals(1, count($extractedMeetings));

    	foreach($extractedMeetings as $extractedMeeting){
    		$this->assertEquals('saturday', $extractedMeeting['time']['day']);
    		$this->assertEquals('NA', $extractedMeeting['meeting_type']);
    	}

    }

    public function testGetLocationLatitudeLongitudeByDefaultAddress()
    {
    	$geoCoordinates = $this->treatmentCenterService->getLocationLatitudeLongitude();

		$this->assertEquals(32.7107334, $geoCoordinates['lat']);
		$this->assertEquals(-117.1607356, $geoCoordinates['lng']);
    }

    public function testGetLocationLatitudeLongitudeByManuallyEnteredAddress()
    {
    	$geoCoordinates = $this->treatmentCenterService->getLocationLatitudeLongitude($this->manualAddress);

		$this->assertEquals(32.8609163, $geoCoordinates['lat']);
		$this->assertEquals(-117.2383307, $geoCoordinates['lng']);
    }

    public function testCalculateMeetingDistancesFromLocation()
    {
    	$meetingsWithDistances = $this->treatmentCenterService->calculateMeetingDistanceFromLocation(['lat' => 37.318116,'lng' => -121.992233],$this->meetingsForDistances);

		$this->assertEquals(6.014824, $meetingsWithDistances[0]['distanceFromOrigin']);
		$this->assertEquals(6.566884, $meetingsWithDistances[1]['distanceFromOrigin']);
    }

    public function testSortMeetingsFromOrigin()
    {
    	$sortedMeetings = $this->treatmentCenterService->sortMeetingsFromOrigin($this->meetingsToBeSortedByDistance);

		$this->assertEquals(30201, $sortedMeetings[0]['id']);
		$this->assertEquals(29555, $sortedMeetings[1]['id']);
    }


}
