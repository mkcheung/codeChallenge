<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller as AppBundleControllers;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\TreatmentCenterService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Mockery;

class ChallengeControllerTest extends WebTestCase
{

	protected $challengeController;
    protected $controllerContainer;
    protected $mockTreatmentCenterService;
	protected $mockRequest;
    protected $mockResponse;
    protected $mockTwigEngine;

	protected $requestInput = [
			'address' => [
				"street_address" => "517 4th Ave.",
				"city" => "San Diego",
				"state" => "CA",
				"zip_code" => 92101
				],
			'day' => 'monday',
			'meeting_type' => [
				'AA'
			]
		];

    protected $treatmentCenterServiceOutput = [
      'meetings' =>
        [
          0 =>
            [
              'id' => 18108,
              'time_id' => 15440,
              'address_id' => 15796,
              'type' => 'MeetingItem',
              'details' => 'Format: Just For Today',
              'meeting_type' => 'AA',
              'meeting_name' => 'Noon At The Beach',
              'language' => 'English',
              'raw_address' => 'Church in alley, 1050 Thomas st., san_diego, , CA',
              'location' => 'Church in alley',
              'address' => [
                  'id' => 15796,
                  'street' => '',
                  'zip' => 92103,
                  'city' => "San Diego",
                  'state_abbr' => "CA",
                  'lat' => 32.746085,
                  'lng' => -117.170517,
                ],
              'time' => [
              ],
              'distanceFromOrigin' => 2.507733
            ]
        ],
      'meetingDay' => 'Monday',
      'meetingTypes' =>
        [
          'AA'
        ],
      'locationAddress' =>
        [
          'street_address' => '517 4th Ave.',
          'city' => 'San Diego',
          'state' => 'CA',
          'zip_code' => '92101'
        ]
    ];

    public function setUp()
    {
        $this->challengeController = new AppBundleControllers\ChallengeController();
        $this->controllerContainer = Mockery::mock(Container::class);
        $this->mockTreatmentCenterService = Mockery::mock(TreatmentCenterService::class);
    	$this->mockRequest = Mockery::mock(Request::class);
        $this->mockResponse = Mockery::mock(Response::class);
    	$this->mockRequest->request = Mockery::mock(ParameterBag::class);
        $this->mockTwigEngine = Mockery::mock(TwigEngine::class);
    }

    public function testMeetingsFromLocationAction()
    {

        $this->mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn($this->requestInput);

        $this->mockResponse
            ->shouldReceive('setSharedMaxAge')
            ->andReturnSelf();

        $this->mockTwigEngine
            ->shouldReceive('renderResponse')
            ->andReturn($this->mockResponse);

        $this->controllerContainer
            ->shouldReceive('get')
            ->with('templating')
            ->andReturn($this->mockTwigEngine);

        $this->controllerContainer
            ->shouldReceive('get')
            ->andReturn($this->mockTreatmentCenterService);

        $this->controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $this->mockTreatmentCenterService
            ->shouldReceive('getTreatmentCenters')
            ->andReturn($this->treatmentCenterServiceOutput);

        $this->challengeController
            ->setContainer($this->controllerContainer);
        $results = $this->challengeController
            ->meetingsFromLocationAction($this->mockRequest);
    }

//    public function testSomething()
//    {
//
//        $this->mockTreatmentCenterService
//            ->shouldReceive('getTreatmentCenters')
//            ->andReturn($this->treatmentCenterServiceOutput);
//
//        $container = new Container();
//
//        $this->challengeController
//            ->setContainer($container);
//
//    }

}
