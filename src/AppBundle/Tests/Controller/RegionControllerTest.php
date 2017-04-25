<?php

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

class RegionControllerTest extends WebTestCase
{

    public function testIndexAction()
    {

        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockResponse        = Mockery::mock(Response::class);


        $mockRegion1    = Mockery::mock(Region::class);
        $mockRegion2    = Mockery::mock(Region::class);
        $mockRegionRepo = Mockery::mock(EntityRepository::class);
        $mockRegionRepo
            ->shouldReceive('findAll')
            ->andReturn([$mockRegion1, $mockRegion2]);

        $mockRegistry = Mockery::mock(Registry::class);
        $mockRegistry
            ->shouldReceive('getRepository')
            ->andReturn($mockRegionRepo);


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

        $regionController
            ->setContainer($controllerContainer);

        $actionResponse = $regionController->indexAction();

        $this->assertInstanceOf(Response::class, $actionResponse);
    }

    public function testCreateAction()
    {
        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockRegionService
            ->shouldReceive('createRegion')
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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $something = $regionController->createAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testCreateActionErrorsFound()
    {
        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockRegionService
            ->shouldReceive('createRegion')
            ->andReturn(['region: This value should not be blank.']);

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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $something = $regionController->createAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testCreateActionDisplayForm()
    {
        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

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
            ->andReturn('/region/create/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $actionResponse = $regionController->createAction($mockRequest);

        $this->assertInstanceOf(Response::class, $actionResponse);

    }

    public function testEditAction()
    {

        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockRegionService
            ->shouldReceive('editRegion')
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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $something = $regionController->editAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testEditActionErrorsFound()
    {
        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockRegionService
            ->shouldReceive('editRegion')
            ->andReturn(['region: This value should not be blank.']);

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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $something = $regionController->editAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

    public function testEditActionDisplayForm()
    {
        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

        $mockRegion     = Mockery::mock(Region::class);
        $mockRegionRepo = Mockery::mock(EntityRepository::class);
        $mockRegionRepo
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);

        $mockRegistry = Mockery::mock(Registry::class);
        $mockRegistry
            ->shouldReceive('getRepository')
            ->andReturn($mockRegionRepo);


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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

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
            ->andReturn('/region/edit/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $actionResponse = $regionController->editAction($mockRequest);

        $this->assertInstanceOf(Response::class, $actionResponse);

    }

    public function testDeleteAction()
    {

        $regionController    = new AppBundleControllers\RegionController();
        $controllerContainer = Mockery::mock(Container::class);
        $mockRegionService   = Mockery::mock(RegionService::class);

        $mockFlashBag = Mockery::mock(FlashBag::class);
        $mockFlashBag
            ->shouldReceive('add')
            ->andReturnSelf();

        $mockSession = Mockery::mock(Session::class);
        $mockSession
            ->shouldReceive('getFlashBag')
            ->andReturn($mockFlashBag);

        $mockRegionService
            ->shouldReceive('deleteRegion')
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
            ->with('app.region_service')
            ->andReturn($mockRegionService);

        $mockRouter = Mockery::mock(Router::class);
        $mockRouter
            ->shouldReceive('generate')
            ->andReturn('/');

        $controllerContainer
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter);

        $regionController
            ->setContainer($controllerContainer);

        $something = $regionController->deleteAction($mockRequest);
        $this->assertInstanceof(RedirectResponse::class, $something);
        $this->assertEquals('/', $something->getTargetUrl());
    }

}
