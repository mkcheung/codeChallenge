<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/25/17
 * Time: 11:07 AM
 */

namespace AppBundle\Tests\Controller;

use AppBundle\Controller as AppBundleControllers;
use AppBundle\Entity\MeetingType;
use AppBundle\Entity\Region;
use AppBundle\Service\RegionService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Service\TreatmentCenterService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Mockery;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class MeetingTypeControllerTest extends WebTestCase
{

    public function testIndexAction()
    {

        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockResponse        = Mockery::mock(Response::class);


        $mockMeetingType1    = Mockery::mock(MeetingType::class);
        $mockMeetingType2    = Mockery::mock(MeetingType::class);
        $mockMeetingTypeRepo = Mockery::mock(EntityRepository::class);
        $mockMeetingTypeRepo
            ->shouldReceive('findAll')
            ->andReturn([$mockMeetingType1, $mockMeetingType2]);

        $mockRegistry = Mockery::mock(Registry::class);
        $mockRegistry
            ->shouldReceive('getRepository')
            ->andReturn($mockMeetingTypeRepo);


        $controllerContainer
            ->shouldReceive('has')
            ->with('doctrine')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('get')
            ->with('doctrine')
            ->andReturn($mockRegistry);

        $controllerContainer
            ->shouldReceive('has')
            ->with('templating')
            ->andReturn(true);

        $mockTwigEngine = Mockery::mock(TwigEngine::class);
        $mockTwigEngine
            ->shouldReceive('renderResponse')
            ->andReturn($mockResponse);

        $controllerContainer
            ->shouldReceive('get')
            ->with('templating')
            ->andReturn($mockTwigEngine);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $actionResponse = $meetingTypeController->indexAction();

        $this->assertInstanceOf(Response::class, $actionResponse);
    }

    public function testCreateAction()
    {
        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockMeetingTypeService
            ->shouldReceive('createMeetingType')
            ->andReturn([]);

        $mockRequest = Mockery::mock(Request::class);

        $mockRequest
            ->shouldReceive('isMethod')
            ->with('POST')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->with('session')
            ->andReturn(false);

        $controllerContainer
            ->shouldReceive('get')
            ->with('session')
            ->andReturn($mockSession);

        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $something = $meetingTypeController->createAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testCreateActionErrorsFound()
    {
        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockMeetingTypeService
            ->shouldReceive('createMeetingType')
            ->andReturn(['meetingType: This value should not be blank.']);

        $mockRequest = Mockery::mock(Request::class);

        $mockRequest
            ->shouldReceive('isMethod')
            ->with('POST')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->with('session')
            ->andReturn(false);

        $controllerContainer
            ->shouldReceive('get')
            ->with('session')
            ->andReturn($mockSession);

        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $something = $meetingTypeController->createAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testCreateActionDisplayForm()
    {
        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockRequest = Mockery::mock(Request::class);

        $mockRequest
            ->shouldReceive('isMethod')
            ->with('POST')
            ->andReturn(false);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);


        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockResponse = Mockery::mock(Response::class);

        $mockTemplate = Mockery::mock(Template::class);
        $mockTemplate
            ->shouldReceive('renderResponse')
            ->andReturn($mockResponse);

        $mockTwigEngine = Mockery::mock(TwigEngine::class);
        $mockTwigEngine
            ->shouldReceive('renderResponse')
            ->andReturn($mockResponse);

        $mockFormView = Mockery::mock(FormView::class);
        $mockForm     = Mockery::mock(Form::class);
        $mockForm
            ->shouldReceive('createView')
            ->andReturn($mockFormView);
        $mockFormFactory = Mockery::mock(FormFactory::class);
        $mockFormFactory
            ->shouldReceive('create')
            ->andReturn($mockForm);

        $controllerContainer
            ->shouldReceive('has')
            ->with('templating')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('get')
            ->with('templating')
            ->andReturn($mockTwigEngine);

        $controllerContainer
            ->shouldReceive('get')
            ->with('form.factory')
            ->andReturn($mockFormFactory);

        $controllerContainer
            ->shouldReceive('createForm')
            ->andReturn($mockForm);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/meetingType/create/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $actionResponse = $meetingTypeController->createAction($mockRequest);

        $this->assertInstanceOf(Response::class, $actionResponse);

    }

    public function testEditAction()
    {

        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockMeetingTypeService
            ->shouldReceive('editMeetingType')
            ->andReturn([]);

        $mockRequest = Mockery::mock(Request::class);

        $mockRequest
            ->shouldReceive('isMethod')
            ->with('POST')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->with('session')
            ->andReturn(false);

        $controllerContainer
            ->shouldReceive('get')
            ->with('session')
            ->andReturn($mockSession);

        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $something = $meetingTypeController->editAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testEditActionErrorsFound()
    {
        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockMeetingTypeService
            ->shouldReceive('editMeetingType')
            ->andReturn(['meetingType: This value should not be blank.']);

        $mockRequest = Mockery::mock(Request::class);

        $mockRequest
            ->shouldReceive('isMethod')
            ->with('POST')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->with('session')
            ->andReturn(false);

        $controllerContainer
            ->shouldReceive('get')
            ->with('session')
            ->andReturn($mockSession);

        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $something = $meetingTypeController->editAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testEditActionDisplayForm()
    {
        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockMeetingType     = Mockery::mock(MeetingType::class);
        $mockMeetingTypeRepo = Mockery::mock(EntityRepository::class);
        $mockMeetingTypeRepo
            ->shouldReceive('findOneBy')
            ->andReturn($mockMeetingType);

        $mockRegistry = Mockery::mock(Registry::class);
        $mockRegistry
            ->shouldReceive('getRepository')
            ->andReturn($mockMeetingTypeRepo);


        $mockRequest = Mockery::mock(Request::class);

        $mockRequest
            ->shouldReceive('isMethod')
            ->with('POST')
            ->andReturn(false);

        $mockRequest->query = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                'id' => 1,
            ]);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockResponse = Mockery::mock(Response::class);

        $mockTemplate = Mockery::mock(Template::class);
        $mockTemplate
            ->shouldReceive('renderResponse')
            ->andReturn($mockResponse);

        $mockTwigEngine = Mockery::mock(TwigEngine::class);
        $mockTwigEngine
            ->shouldReceive('renderResponse')
            ->andReturn($mockResponse);

        $mockFormView = Mockery::mock(FormView::class);
        $mockForm     = Mockery::mock(Form::class);
        $mockForm
            ->shouldReceive('createView')
            ->andReturn($mockFormView);
        $mockFormFactory = Mockery::mock(FormFactory::class);
        $mockFormFactory
            ->shouldReceive('create')
            ->andReturn($mockForm);

        $controllerContainer
            ->shouldReceive('has')
            ->with('templating')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->with('doctrine')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('get')
            ->with('templating')
            ->andReturn($mockTwigEngine);

        $controllerContainer
            ->shouldReceive('get')
            ->with('form.factory')
            ->andReturn($mockFormFactory);

        $controllerContainer
            ->shouldReceive('get')
            ->with('doctrine')
            ->andReturn($mockRegistry);

        $controllerContainer
            ->shouldReceive('createForm')
            ->andReturn($mockForm);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/meetingType/edit/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $actionResponse = $meetingTypeController->editAction($mockRequest);

        $this->assertInstanceOf(Response::class, $actionResponse);

    }

    public function testDeleteAction()
    {

        $meetingTypeController    = new AppBundleControllers\MeetingTypeController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockMeetingTypeService   = Mockery::mock(MeetingTypeService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockMeetingTypeService
            ->shouldReceive('deleteMeetingType')
            ->andReturn([]);

        $mockRequest = Mockery::mock(Request::class);

        $controllerContainer
            ->shouldReceive('has')
            ->andReturn(true);

        $controllerContainer
            ->shouldReceive('has')
            ->with('session')
            ->andReturn(false);

        $controllerContainer
            ->shouldReceive('get')
            ->with('session')
            ->andReturn($mockSession);

        $controllerContainer
            ->shouldReceive('get')
            ->with('app.meeting_type_service')
            ->andReturn($mockMeetingTypeService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $meetingTypeController
            ->setContainer($controllerContainer);

        $something = $meetingTypeController->deleteAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

}
