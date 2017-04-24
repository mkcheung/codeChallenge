<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Region;
use AppBundle\Service\RegionService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Mockery;

class RegionServiceTest extends WebTestCase
{

    public function testCreateRegion()
    {

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturnNull();

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'region'        => 'California',
                'region_abbrev' => 'CA',
            ]);

        $regionService->createRegion($mockRequest);
    }

    public function testCreateRegionWithMissingRegionAndRegionAbbrev()
    {
        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockConstraintViolationRegion = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationRegion
            ->shouldReceive('getPropertyPath')
            ->andReturn('region');
        $mockConstraintViolationRegion
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationRegionAbbrev = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationRegionAbbrev
            ->shouldReceive('getPropertyPath')
            ->andReturn('regionAbbrev');
        $mockConstraintViolationRegionAbbrev
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(2);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationRegion);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationRegionAbbrev);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'region'        => '',
                'region_abbrev' => '',
            ]);

        $returnedErrors = $regionService->createRegion($mockRequest);
        $this->assertEquals('region: This value should not be blank.', $returnedErrors[0]);
        $this->assertEquals('regionAbbrev: This value should not be blank.', $returnedErrors[1]);
    }

    public function testCreateRegionWithMissingRegion()
    {
        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockConstraintViolation = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolation
            ->shouldReceive('getPropertyPath')
            ->andReturn('region');
        $mockConstraintViolation
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->andReturn($mockConstraintViolation);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'region'        => '',
                'region_abbrev' => 'CA',
            ]);

        $returnedErrors = $regionService->createRegion($mockRequest);
        $this->assertEquals('region: This value should not be blank.', $returnedErrors[0]);
    }

    public function testCreateRegionWithMissingRegionAbbrev()
    {
        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockConstraintViolation = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolation
            ->shouldReceive('getPropertyPath')
            ->andReturn('regionAbbrev');
        $mockConstraintViolation
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->andReturn($mockConstraintViolation);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'region'        => 'California',
                'region_abbrev' => '',
            ]);

        $returnedErrors = $regionService->createRegion($mockRequest);
        $this->assertEquals('regionAbbrev: This value should not be blank.', $returnedErrors[0]);
    }

    public function testEditRegion()
    {
        $mockRegion = Mockery::mock(Region::class);
        $mockRegion->shouldReceive('setRegion')->andReturnSelf();
        $mockRegion->shouldReceive('setRegionAbbrev')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturnNull();

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'region'        => 'Arizona',
                'region_abbrev' => 'AZ',
            ]);

        $regionService->editRegion($mockRequest);
    }

    public function testEditRegionMissingRegionAndRegionAbbrev()
    {

        $mockConstraintViolationRegion = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationRegion
            ->shouldReceive('getPropertyPath')
            ->andReturn('region');
        $mockConstraintViolationRegion
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationRegionAbbrev = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationRegionAbbrev
            ->shouldReceive('getPropertyPath')
            ->andReturn('regionAbbrev');
        $mockConstraintViolationRegionAbbrev
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');


        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(2);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationRegion);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationRegionAbbrev);

        $mockRegion = Mockery::mock(Region::class);
        $mockRegion->shouldReceive('setRegion')->andReturnSelf();
        $mockRegion->shouldReceive('setRegionAbbrev')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'region'        => '',
                'region_abbrev' => '',
            ]);

        $returnedErrors = $regionService->editRegion($mockRequest);
        $this->assertEquals('region: This value should not be blank.', $returnedErrors[0]);
        $this->assertEquals('regionAbbrev: This value should not be blank.', $returnedErrors[1]);
    }

    public function testEditRegionMissingRegion()
    {

        $mockConstraintViolation = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolation
            ->shouldReceive('getPropertyPath')
            ->andReturn('region');
        $mockConstraintViolation
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->andReturn($mockConstraintViolation);


        $mockRegion = Mockery::mock(Region::class);
        $mockRegion->shouldReceive('setRegion')->andReturnSelf();
        $mockRegion->shouldReceive('setRegionAbbrev')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);


        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'region'        => '',
                'region_abbrev' => 'AZ',
            ]);

        $returnedErrors = $regionService->editRegion($mockRequest);
        $this->assertEquals('region: This value should not be blank.', $returnedErrors[0]);
    }

    public function testEditRegionMissingRegionAbbrev()
    {

        $mockConstraintViolation = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolation
            ->shouldReceive('getPropertyPath')
            ->andReturn('regionAbbrev');
        $mockConstraintViolation
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->andReturn($mockConstraintViolation);


        $mockRegion = Mockery::mock(Region::class);
        $mockRegion->shouldReceive('setRegion')->andReturnSelf();
        $mockRegion->shouldReceive('setRegionAbbrev')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);


        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'region'        => 'Arizona',
                'region_abbrev' => '',
            ]);

        $returnedErrors = $regionService->editRegion($mockRequest);
        $this->assertEquals('regionAbbrev: This value should not be blank.', $returnedErrors[0]);
    }

    public function testDeleteRegion()
    {

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockRegion = Mockery::mock(Region::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);

        $validator = Mockery::mock(ValidatorInterface::class);

        $em = Mockery::mock(EntityManager::class);
        $em->shouldReceive('remove')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $regionService = new RegionService($em, $mockEntityRepository, $validator);

        $mockRequest        = Mockery::mock(Request::class);
        $mockRequest->query = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                'id' => 1,
            ]);

        $regionService->deleteRegion($mockRequest);
    }
}
