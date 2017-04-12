<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\MeetingType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;
use AppBundle\Service\TreatmentCenterService as TreatmentCenterService;

use JsonRPC\Client as JsonRPCClient;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Mockery;

class TreatmentCenterServiceTest extends WebTestCase
{

    protected $originAddress = [
        "street_address" => "517 4th Ave.",
        "city"           => "San Diego",
        "state"          => "CA",
        "zip_code"       => 92101,
    ];

    protected $manualAddress = [
        "street_address" => "4657 Cranbrook Ct.",
        "city"           => "La Jolla",
        "state"          => "CA",
        "zip_code"       => 92037,
    ];

    protected $sanJoseAddress = [
        "street_address" => "502 South Park Dr.",
        "city"           => "San Jose",
        "state"          => "CA",
        "zip_code"       => 95129,
    ];

    protected $meetingTypes = [
        "AA" => "Alcoholics Anonymous",
        "MA" => "Marijuana Anonymous",
        "OA" => "Overeaters Anonymous",
        "NA" => "Alcoholics Anonymous",
    ];

    protected $defaultMeetingTypes = [];

    protected $meetings = [
        [
            "id"           => 29555,
            "time_id"      => 15430,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker",
            "meeting_type" => "AA",
            "meeting_name" => "Saturday Night Live",
            "language"     => "English",
            "raw_address"  => "San jose, 1388 S Bascom Ave San Jose CA, San Jose, , CA",
            "location"     => "San jose",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15430,
                "day"  => "saturday",
                "hour" => 0,
            ],
        ],
        [
            "id"           => 29557,
            "time_id"      => 18430,
            "address_id"   => 17132,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker",
            "meeting_type" => "AA",
            "meeting_name" => "Saturday Night Live 2",
            "language"     => "English",
            "raw_address"  => "San jose, 1118 S Bascom Ave San Jose CA, San Jose, , CA",
            "location"     => "San jose",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 16000,
                "day"  => "saturday",
                "hour" => 0,
            ],
        ],
        [
            "id"           => 30176,
            "time_id"      => 15523,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker , Participation",
            "meeting_type" => "NA",
            "meeting_name" => "Newcomer Introduction To AA",
            "language"     => ".",
            "raw_address"  => "alono club of san jose, 1122 Fair Ave, san_jose, , CA",
            "location"     => "alono club of san jose",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15523,
                "day"  => "thursday",
                "hour" => 1800,
            ],
        ],
        [
            "id"           => 91968,
            "time_id"      => 15419,
            "address_id"   => 37165,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker, Discussion/Participation",
            "meeting_type" => "NA",
            "meeting_name" => "Queen Of Apostles Church",
            "language"     => "English",
            "raw_address"  => "Queen of Apostles Church, 4911 Moorpark Avenue, San Jose, , CA",
            "location"     => "Queen of Apostles Church",
            "address"      => [
                "id"         => 37165,
                "street"     => "4911 Moorpark Ave",
                "zip"        => "95129",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.313952",
                "lng"        => "-121.99026510345",
            ],
            "time"         => [
                "id"   => 15419,
                "day"  => "saturday",
                "hour" => 2000,
            ],
        ],
        [
            "id"           => 92004,
            "time_id"      => 15437,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format:",
            "meeting_type" => "NA",
            "meeting_name" => "San Thomas Park Recreation Center",
            "language"     => ".",
            "raw_address"  => "San Thomas Park Recreation Center, 4093 Valerie Drive, San Jose, , CA",
            "location"     => "San Thomas Park Recreation Center",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15437,
                "day"  => "wednesday",
                "hour" => 2000,
            ],
        ],
        [
            "id"           => 92005,
            "time_id"      => 15478,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format:",
            "meeting_type" => "NA",
            "meeting_name" => "San Thomas Park Recreation Center",
            "language"     => ".",
            "raw_address"  => "San Thomas Park Recreation Center, 4093 Valerie Drive, San Jose, , CA",
            "location"     => "San Thomas Park Recreation Center",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15478,
                "day"  => "tuesday",
                "hour" => 2000,
            ],
        ],
    ];

    protected $meetingsFromSanJoseInputData = [
        [
            "id"           => 29555,
            "time_id"      => 15430,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker",
            "meeting_type" => "AA",
            "meeting_name" => "Saturday Night Live",
            "language"     => "English",
            "raw_address"  => 'San jose, 1388 S Bascom Ave San Jose CA, San Jose, , CA',
            "location"     => "San jose",
            "address"      => [
                "id"         => 17832,
                "street"     => '',
                "zip"        => '95112',
                "city"       => 'San Jose',
                "state_abbr" => 'CA',
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15430,
                "day"  => "saturday",
                "hour" => 0,
            ],
        ],
        [
            "id"           => 91826,
            "time_id"      => 15436,
            "address_id"   => 37132,
            "type"         => "MeetingItem",
            "details"      => "Format:",
            "meeting_type" => "NA",
            "meeting_name" => "Saint Timothy's Lutheran Church",
            "language"     => ".",
            "raw_address"  => "Saint Timothy's Lutheran Church, 5100 Camden Avenue, San Jose, , CA",
            "location"     => "Saint Timothy's Lutheran Church",
            "address"      => [
                "id"         => 37132,
                "street"     => '5100 Camden Ave',
                "zip"        => '95124',
                "city"       => 'San Jose',
                "state_abbr" => 'CA',
                "lat"        => "37.246971888889",
                "lng"        => "-121.9086842963",
            ],
            "time"         => [
                "id"   => 15436,
                "day"  => "wednesday",
                "hour" => 1900,
            ],
        ],
        [
            "id"           => 60969,
            "time_id"      => 15558,
            "address_id"   => 25484,
            "type"         => "MeetingItem",
            "details"      => "Format: Contact: Rick - 408-225-9780",
            "meeting_type" => "OA",
            "meeting_name" => "Church Of The Good Shepard",
            "language"     => "English",
            "raw_address"  => "Church Of The Good Shepard, 1550 Meridian Avenue, San Jose, , CA",
            "location"     => "Church Of The Good Shepard",
            "address"      => [
                "id"         => 25484,
                "street"     => '1550 Meridian Ave',
                "zip"        => '95125',
                "city"       => 'San Jose',
                "state_abbr" => 'CA',
                "lat"        => "37.295636",
                "lng"        => "-121.91355",
            ],
            "time"         => [
                "id"   => 15558,
                "day"  => "friday",
                "hour" => 1000,
            ],
        ],
        [
            "id"           => 30176,
            "time_id"      => 15523,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker , Participation",
            "meeting_type" => "AA",
            "meeting_name" => "Newcomer Introduction To AA",
            "language"     => ".",
            "raw_address"  => "alono club of san jose, 1122 Fair Ave, san_jose, , CA",
            "location"     => "alono club of san jose",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15523,
                "day"  => "thursday",
                "hour" => 1800,
            ],
        ],

    ];

    protected $meetingsFromSanJoseSanDiego = [
        [
            "id"           => 29555,
            "time_id"      => 15430,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker",
            "meeting_type" => "AA",
            "meeting_name" => "Saturday Night Live",
            "language"     => "English",
            "raw_address"  => 'San jose, 1388 S Bascom Ave San Jose CA, San Jose, , CA',
            "location"     => "San jose",
            "address"      => [
                "id"         => 17832,
                "street"     => '',
                "zip"        => '95112',
                "city"       => 'San Jose',
                "state_abbr" => 'CA',
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15430,
                "day"  => "saturday",
                "hour" => 0,
            ],
        ],
        [
            "id"           => 91826,
            "time_id"      => 15436,
            "address_id"   => 37132,
            "type"         => "MeetingItem",
            "details"      => "Format:",
            "meeting_type" => "NA",
            "meeting_name" => "Saint Timothy's Lutheran Church",
            "language"     => ".",
            "raw_address"  => "Saint Timothy's Lutheran Church, 5100 Camden Avenue, San Jose, , CA",
            "location"     => "Saint Timothy's Lutheran Church",
            "address"      => [
                "id"         => 37132,
                "street"     => '5100 Camden Ave',
                "zip"        => '95124',
                "city"       => 'San Jose',
                "state_abbr" => 'CA',
                "lat"        => "37.246971888889",
                "lng"        => "-121.9086842963",
            ],
            "time"         => [
                "id"   => 15436,
                "day"  => "wednesday",
                "hour" => 1900,
            ],
        ],
        [
            "id"           => 18108,
            "time_id"      => 15440,
            "address_id"   => 15796,
            "type"         => "MeetingItem",
            "details"      => "Format: Just For Today",
            "meeting_type" => "AA",
            "meeting_name" => "Noon At The Beach",
            "language"     => "English",
            "raw_address"  => "Church in alley, 1050 Thomas st., san_diego, , CA",
            "location"     => "Church in alley",
            "address"      => [
                "id"         => 15796,
                "street"     => "",
                "zip"        => "92103",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.746085",
                "lng"        => "-117.170517",
            ],
            "time"         => [
                "id"   => 15419,
                "day"  => "saturday",
                "hour" => 2000,
            ],
        ],
        [
            "id"           => 18055,
            "time_id"      => 15457,
            "address_id"   => 15796,
            "type"         => "MeetingItem",
            "details"      => "Format:",
            "meeting_type" => "AA",
            "meeting_name" => "Unnamed Meeting",
            "language"     => ".",
            "raw_address"  => "San Diego, 90 Day Avenue, san_diego, , CA",
            "location"     => "San Diego",
            "address"      => [
                "id"         => 15796,
                "street"     => "",
                "zip"        => "92103",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.746085",
                "lng"        => "-117.170517",
            ],
            "time"         => [
                "id"   => 15457,
                "day"  => "sunday",
                "hour" => 1000,
            ],
        ],
    ];

    protected $meetingsFromDefault = [
        [
            "id"           => 18108,
            "time_id"      => 15440,
            "address_id"   => 15796,
            "type"         => "MeetingItem",
            "details"      => "Format: Just For Today",
            "meeting_type" => "AA",
            "meeting_name" => "Noon At The Beach",
            "language"     => "English",
            "raw_address"  => "Church in alley, 1050 Thomas st., san_diego, , CA",
            "location"     => "Church in alley",
            "address"      => [
                "id"         => 15796,
                "street"     => "",
                "zip"        => "92103",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.746085",
                "lng"        => "-117.170517",
            ],
            "time"         => [
                "id"   => 15419,
                "day"  => "saturday",
                "hour" => 2000,
            ],
        ],
        [
            "id"           => 18055,
            "time_id"      => 15457,
            "address_id"   => 15796,
            "type"         => "MeetingItem",
            "details"      => "Format:",
            "meeting_type" => "AA",
            "meeting_name" => "Unnamed Meeting",
            "language"     => ".",
            "raw_address"  => "San Diego, 90 Day Avenue, san_diego, , CA",
            "location"     => "San Diego",
            "address"      => [
                "id"         => 15796,
                "street"     => "",
                "zip"        => "92103",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.746085",
                "lng"        => "-117.170517",
            ],
            "time"         => [
                "id"   => 15457,
                "day"  => "sunday",
                "hour" => 1000,
            ],
        ],
        [
            "id"           => 18056,
            "time_id"      => 15492,
            "address_id"   => 15797,
            "type"         => "MeetingItem",
            "details"      => "Format: Closed",
            "meeting_type" => "AA",
            "meeting_name" => "A Spiritual Way Of Life",
            "language"     => ".",
            "raw_address"  => "Allied Gardens, 5107 Orcutt Ave, san diego, , CA",
            "location"     => "Allied Gardens",
            "address"      => [
                "id"         => 15797,
                "street"     => "5107 Orcutt Ave",
                "zip"        => "92120",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.791229875",
                "lng"        => "-117.0836145",
            ],
            "time"         => [
                "id"   => 15492,
                "day"  => "thursday",
                "hour" => 1730,
            ],
        ],
        [
            "id"           => 57270,
            "time_id"      => 15416,
            "address_id"   => 23369,
            "type"         => "MeetingItem",
            "details"      => "Format: O,Ns,Wa",
            "meeting_type" => "MA",
            "meeting_name" => "Weedless Warriors",
            "language"     => ".",
            "raw_address"  => "VFVSD Recovery Home, 4119 Pacific Highway, San Diego, , California",
            "location"     => "VFVSD Recovery Home",
            "address"      => [
                "id"         => 23369,
                "street"     => "4119 Pacific Hwy",
                "zip"        => "92110",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.744478625",
                "lng"        => "-117.1929615",
            ],
            "time"         => [
                "id"   => 15416,
                "day"  => "monday",
                "hour" => 1900,
            ],
        ],
        [
            "id"           => 60678,
            "time_id"      => 15593,
            "address_id"   => 15796,
            "type"         => "MeetingItem",
            "details"      => "Format: Contact: Chris - 619-518-2653",
            "meeting_type" => "OA",
            "meeting_name" => "Oasis",
            "language"     => "English",
            "raw_address"  => "Oasis, 6304 Riverdale, San Diego, , CA",
            "location"     => "Oasis",
            "address"      => [
                "id"         => 15796,
                "street"     => "",
                "zip"        => "92103",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.746085",
                "lng"        => "-117.170517",
            ],
            "time"         => [
                "id"   => 15593,
                "day"  => "sunday",
                "hour" => 1330,
            ],
        ],
        [
            "id"           => 87638,
            "time_id"      => 15419,
            "address_id"   => 35880,
            "type"         => "MeetingItem",
            "details"      => "Format: Non-Smoking, Discussion/Participation, Ip Study, Topic",
            "meeting_type" => "NA",
            "meeting_name" => "2265 Flower Avenue #B",
            "language"     => "Spanish",
            "raw_address"  => "2265 Flower Avenue #B, 2265 Flower Avenue #B, San Diego, , CA",
            "location"     => "2265 Flower Avenue #B",
            "address"      => [
                "id"         => 35880,
                "street"     => "2265 Flower Ave",
                "zip"        => "92154",
                "city"       => "San Diego",
                "state_abbr" => "CA",
                "lat"        => "32.575946622222",
                "lng"        => "-117.08599588889",
            ],
            "time"         => [
                "id"   => 15419,
                "day"  => "saturday",
                "hour" => 2000,
            ],
        ],
    ];

    protected $meetingsForDistances = [
        [
            "id"           => 30201,
            "time_id"      => 15419,
            "address_id"   => 17935,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker Meeting",
            "meeting_type" => "AA",
            "meeting_name" => "Second Tradition Group Speaker Meeting",
            "language"     => "English",
            "raw_address"  => "Lincoln Glen Church, 2700 Booksin Ave, San Jose, , CA",
            "location"     => "Lincoln Glen Church",
            "address"      => [
                "id"         => 17935,
                "street"     => "2700 Booksin Ave",
                "zip"        => "95125",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.275002",
                "lng"        => "-121.897159",
            ],
            "time"         => [
                "id"   => 15419,
                "day"  => "saturday",
                "hour" => 2000,
            ],
        ],
        [
            "id"           => 29555,
            "time_id"      => 15430,
            "address_id"   => 17832,
            "type"         => "MeetingItem",
            "details"      => "Format: Speaker",
            "meeting_type" => "AA",
            "meeting_name" => "Saturday Night Live",
            "language"     => "English",
            "raw_address"  => "San jose, 1118 S Bascom Ave San Jose CA, San Jose, , CA",
            "location"     => "San jose",
            "address"      => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"         => [
                "id"   => 15430,
                "day"  => "saturday",
                "hour" => 0,
            ],
        ],
    ];

    protected $meetingsToBeSortedByDistance = [
        [
            "id"                 => 29555,
            "time_id"            => 15430,
            "address_id"         => 17832,
            "type"               => "MeetingItem",
            "details"            => "Format: Speaker",
            "meeting_type"       => "AA",
            "meeting_name"       => "Saturday Night Live",
            "language"           => "English",
            "raw_address"        => "San jose, 1118 S Bascom Ave San Jose CA, San Jose, , CA",
            "location"           => "San jose",
            "address"            => [
                "id"         => 17832,
                "street"     => "",
                "zip"        => "95112",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.370040",
                "lng"        => "-121.892093",
            ],
            "time"               => [
                "id"   => 15430,
                "day"  => "saturday",
                "hour" => 0,
            ],
            "distanceFromOrigin" => 6.566884,
        ],
        [
            "id"                 => 30201,
            "time_id"            => 15419,
            "address_id"         => 17935,
            "type"               => "MeetingItem",
            "details"            => "Format: Speaker Meeting",
            "meeting_type"       => "AA",
            "meeting_name"       => "Second Tradition Group Speaker Meeting",
            "language"           => "English",
            "raw_address"        => "Lincoln Glen Church, 2700 Booksin Ave, San Jose, , CA",
            "location"           => "Lincoln Glen Church",
            "address"            => [
                "id"         => 17935,
                "street"     => "2700 Booksin Ave",
                "zip"        => "95125",
                "city"       => "San Jose",
                "state_abbr" => "CA",
                "lat"        => "37.275002",
                "lng"        => "-121.897159",
            ],
            "time"               => [
                "id"   => 15419,
                "day"  => "saturday",
                "hour" => 2000,
            ],
            "distanceFromOrigin" => 6.014824,
        ],
    ];

    protected $JsonRPCClient;
    protected $mockJsonRPCClient;
    protected $treatmentCenterService;
    protected $entityRepository;

    public function setUp()
    {
        // NOTE: Use fully qualified namespace to circumvent issues with type-hinted method parameters
        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->entityRepository  = Mockery::mock(EntityRepository::class);

        $this->mockMeetingTypesArray();

        $this->entityRepository
            ->shouldReceive('findAll')
            ->andReturn($this->defaultMeetingTypes);
    }

    public function mockMeetingTypesArray()
    {

        foreach ($this->meetingTypes as $initials => $fullTerm) {
            $mockedMeetingType = Mockery::mock(MeetingType::class);
            $mockedMeetingType
                ->shouldReceive('getMeetingTypeInitials')
                ->andReturn($initials);
            $this->defaultMeetingTypes[] = $mockedMeetingType;
        }
    }

    public function testGetTreatmentCentersDefaults()
    {

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);
        $mockRequest->query   = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturnNull();

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                "street_address" => '',
                "city"           => '',
                "state"          => '',
                "zip_code"       => '',
                'day'            => '',
            ]);

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();
        $this->mockJsonRPCClient
            ->shouldReceive('execute')
            ->andReturn($this->meetingsFromDefault);

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->originAddress['street_address'], $this->originAddress['city'], $this->originAddress['state'],
            $this->originAddress['zip_code']);
        $treatmentCenterMeetings      = $this->treatmentCenterService->getTreatmentCenters($mockRequest);

        $meetings = $treatmentCenterMeetings['meetings'];
        $this->assertEquals('All Day', $treatmentCenterMeetings['meetingDay']);
        $this->assertEquals(['AA', 'MA', 'OA', 'NA'], $treatmentCenterMeetings['meetingTypes']);
        $this->assertEquals("517 4th Ave.", $treatmentCenterMeetings['locationAddress']['street_address']);
        $this->assertEquals("San Diego", $treatmentCenterMeetings['locationAddress']['city']);
        $this->assertEquals("CA", $treatmentCenterMeetings['locationAddress']['state']);
        $this->assertEquals(92101, $treatmentCenterMeetings['locationAddress']['zip_code']);
        $this->assertEquals(6, count($meetings));

        foreach ($meetings as $meeting) {
            $this->assertContains($meeting['meeting_type'], array_keys($this->meetingTypes));
            $this->assertEquals("San Diego", $meeting['address']['city']);
            $this->assertEquals("CA", $meeting['address']['state_abbr']);
        }
    }

    public function testGetTreatmentCentersUsingGet()
    {

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);
        $mockRequest->query   = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                "street_address" => '502 South Park Dr.',
                "city"           => 'San Jose',
                "state"          => 'CA',
                "zip_code"       => 95129,
                'day'            => 'thursday',
                'meeting_type'   => [
                    'AA',
                ],
            ]);

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();
        $this->mockJsonRPCClient
            ->shouldReceive('execute')
            ->andReturn($this->meetingsFromSanJoseInputData);

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->originAddress['street_address'], $this->originAddress['city'], $this->originAddress['state'],
            $this->originAddress['zip_code']);
        $treatmentCenterMeetings      = $this->treatmentCenterService->getTreatmentCenters($mockRequest);

        $meetings = $treatmentCenterMeetings['meetings'];
        $this->assertEquals('Thursday', $treatmentCenterMeetings['meetingDay']);
        $this->assertEquals(['AA'], $treatmentCenterMeetings['meetingTypes']);
        $this->assertEquals("502 South Park Dr.", $treatmentCenterMeetings['locationAddress']['street_address']);
        $this->assertEquals("San Jose", $treatmentCenterMeetings['locationAddress']['city']);
        $this->assertEquals("CA", $treatmentCenterMeetings['locationAddress']['state']);
        $this->assertEquals(95129, $treatmentCenterMeetings['locationAddress']['zip_code']);
        $this->assertEquals(1, count($meetings));

        foreach ($meetings as $meeting) {
            $this->assertEquals('thursday', $meeting['time']['day']);
            $this->assertEquals('AA', $meeting['meeting_type']);
        }
    }

    public function testGetTreatmentCentersUsingPost()
    {

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);
        $mockRequest->query   = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturnNull();

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                "street_address" => '502 South Park Dr.',
                "city"           => 'San Jose',
                "state"          => 'CA',
                "zip_code"       => 95129,
                'day'            => 'thursday',
                'meeting_type'   => [
                    'AA',
                ],
            ]);

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();
        $this->mockJsonRPCClient
            ->shouldReceive('execute')
            ->andReturn($this->meetingsFromSanJoseInputData);

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->originAddress['street_address'], $this->originAddress['city'], $this->originAddress['state'],
            $this->originAddress['zip_code']);
        $treatmentCenterMeetings      = $this->treatmentCenterService->getTreatmentCenters($mockRequest);

        $meetings = $treatmentCenterMeetings['meetings'];
        $this->assertEquals('Thursday', $treatmentCenterMeetings['meetingDay']);
        $this->assertEquals(['AA'], $treatmentCenterMeetings['meetingTypes']);
        $this->assertEquals("502 South Park Dr.", $treatmentCenterMeetings['locationAddress']['street_address']);
        $this->assertEquals("San Jose", $treatmentCenterMeetings['locationAddress']['city']);
        $this->assertEquals("CA", $treatmentCenterMeetings['locationAddress']['state']);
        $this->assertEquals(95129, $treatmentCenterMeetings['locationAddress']['zip_code']);
        $this->assertEquals(1, count($meetings));

        foreach ($meetings as $meeting) {
            $this->assertEquals('thursday', $meeting['time']['day']);
            $this->assertEquals('AA', $meeting['meeting_type']);
        }
    }

    public function testAssembleCacheKeyDefaultEntryViaGet()
    {

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);
        $mockRequest->query   = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturnNull();

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                "street_address" => '',
                "city"           => '',
                "state"          => '',
                "zip_code"       => null,
                'day'            => ''
            ]);

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $cacheKey = $this->treatmentCenterService->assembleCacheKey($mockRequest);

        $this->assertEquals('default', $cacheKey);
    }

    public function testAssembleCacheKeyDefaultEntryViaPost()
    {

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                "street_address" => '',
                "city"           => '',
                "state"          => '',
                "zip_code"       => null,
                'day'            => ''
            ]);

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $cacheKey = $this->treatmentCenterService->assembleCacheKey($mockRequest);

        $this->assertEquals('default', $cacheKey);
    }

    public function testAssembleCacheKeyManualAddressEntry()
    {

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);
        $mockRequest->query   = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturnNull();

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                "street_address" => '502 South Park Dr.',
                "city"           => 'San Jose',
                "state"          => 'CA',
                "zip_code"       => 95129,
                'day'            => 'thursday',
                'meeting_type'   => [
                    'AA',
                ],
            ]);

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $cacheKey = $this->treatmentCenterService->assembleCacheKey($mockRequest);

        $this->assertEquals('502 South Park Dr.San JoseCA95129thursdayAA', $cacheKey);
    }

    public function testExtractDesiredMeetingsDefaultByDay()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->sanJoseAddress['street_address'], $this->sanJoseAddress['city'], $this->sanJoseAddress['state'],
            $this->sanJoseAddress['zip_code']);

        $this->treatmentCenterService->setDayOfMeeting('saturday');

        $extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetingsFromSanJoseInputData);
        $this->assertEquals(1, count($extractedMeetings));
    }

    public function testExtractDesiredMeetingsByType()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->sanJoseAddress['street_address'], $this->sanJoseAddress['city'], $this->sanJoseAddress['state'],
            $this->sanJoseAddress['zip_code']);

        $this->treatmentCenterService->setMeetingTypes(['AA', 'OA']);

        $extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetingsFromSanJoseInputData);
        $this->assertEquals(3, count($extractedMeetings));
    }

    public function testExtractDesiredMeetingsByDayAndType()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->sanJoseAddress['street_address'], $this->sanJoseAddress['city'], $this->sanJoseAddress['state'],
            $this->sanJoseAddress['zip_code']);

        $this->treatmentCenterService->setDayOfMeeting('saturday');
        $this->treatmentCenterService->setMeetingTypes(['NA']);

        $extractedMeetings = $this->treatmentCenterService->extractDesiredMeetings($this->meetings);
        $this->assertEquals(1, count($extractedMeetings));
    }

    public function testGetLocationLatitudeLongitudeByAddress()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->originAddress['street_address'], $this->originAddress['city'], $this->originAddress['state'],
            $this->originAddress['zip_code']);

        $geoCoordinates = $this->treatmentCenterService->getLocationLatitudeLongitude();

        $this->assertEquals(32.7107334, $geoCoordinates['lat']);
        $this->assertEquals(-117.1607356, $geoCoordinates['lng']);
    }

    public function testGetLocationLatitudeLongitudeByManuallyEnteredAddress()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $geoCoordinates = $this->treatmentCenterService->getLocationLatitudeLongitude();

        $this->assertEquals(32.8609163, $geoCoordinates['lat']);
        $this->assertEquals(-117.2383307, $geoCoordinates['lng']);
    }

    public function testCalculateMeetingDistancesFromLocation()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $meetingsWithDistances = $this->treatmentCenterService->calculateMeetingDistanceFromLocation([
            'lat' => 37.318116,
            'lng' => -121.992233,
        ], $this->meetingsForDistances);

        $this->assertEquals(6.014824, $meetingsWithDistances[0]['distanceFromOrigin']);
        $this->assertEquals(6.566884, $meetingsWithDistances[1]['distanceFromOrigin']);
    }

    public function testSortMeetingsFromOrigin()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $sortedMeetings = $this->treatmentCenterService->sortMeetingsFromOrigin($this->meetingsToBeSortedByDistance);

        $this->assertEquals(30201, $sortedMeetings[0]['id']);
        $this->assertEquals(29555, $sortedMeetings[1]['id']);
    }

    public function testSetStreetAddress()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $this->treatmentCenterService->setStreetAddress('4657 Cranbrook Ct.');

        $this->assertEquals('4657 Cranbrook Ct.', $this->treatmentCenterService->getStreetAddress());
    }

    public function testSetCity()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $this->treatmentCenterService->setCity('La Jolla');

        $this->assertEquals('La Jolla', $this->treatmentCenterService->getCity());
    }

    public function testSetZipCode()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $this->treatmentCenterService->setZipCode('92037');

        $this->assertEquals('92037', $this->treatmentCenterService->getZipCode());
    }

    public function testSetState()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $this->treatmentCenterService->setState('AZ');

        $this->assertEquals('AZ', $this->treatmentCenterService->getState());
    }

    public function testSetMeetingTypes()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $this->treatmentCenterService->setMeetingTypes(['NA', 'MA']);

        $this->assertEquals(2, count($this->treatmentCenterService->getMeetingTypes()));
        $this->assertContains('NA', $this->treatmentCenterService->getMeetingTypes());
        $this->assertContains('MA', $this->treatmentCenterService->getMeetingTypes());
    }

    public function testSetDayOfMeeting()
    {

        $this->mockJsonRPCClient = Mockery::mock('JsonRPC\Client');
        $this->mockJsonRPCClient
            ->shouldReceive('authentication')
            ->andReturnSelf();

        $this->treatmentCenterService = new TreatmentCenterService($this->mockJsonRPCClient, $this->entityRepository,
            $this->manualAddress['street_address'], $this->manualAddress['city'], $this->manualAddress['state'],
            $this->manualAddress['zip_code']);

        $this->treatmentCenterService->setDayOfMeeting('friday');

        $this->assertEquals('friday', $this->treatmentCenterService->getDayOfMeeting());
    }
}
