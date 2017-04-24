<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/24/17
 * Time: 1:06 PM
 */

namespace AppBundle\Tests\Service;

use AppBundle\Entity\MeetingType;
use AppBundle\Service\MeetingTypeService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Mockery;

class MeetingTypeServiceTest extends WebTestCase
{

    public function testCreateMeetingType()
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

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'meeting_type'        => 'Psychological Abuse',
                'meeting_type_initials' => 'PA',
            ]);

        $meetingTypeService->createMeetingType($mockRequest);
    }

    public function testCreateRegionWithMissingMeetingTypeAndMeetingTypeInitials()
    {
        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockConstraintViolationMeetingType = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingType
            ->shouldReceive('getPropertyPath')
            ->andReturn('meeting_type');
        $mockConstraintViolationMeetingType
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationMeetingTypeInitials = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingTypeInitials
            ->shouldReceive('getPropertyPath')
            ->andReturn('meeting_type_initials');
        $mockConstraintViolationMeetingTypeInitials
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
            ->andReturn($mockConstraintViolationMeetingType);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationMeetingTypeInitials);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'meeting_type'        => '',
                'meeting_type_initials' => '',
            ]);

        $returnedErrors = $meetingTypeService->createMeetingType($mockRequest);
        $this->assertEquals('meeting_type: This value should not be blank.', $returnedErrors[0]);
        $this->assertEquals('meeting_type_initials: This value should not be blank.', $returnedErrors[1]);
    }

    public function testCreateRegionWithMissingMeetingType()
    {
        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockConstraintViolationMeetingType = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingType
            ->shouldReceive('getPropertyPath')
            ->andReturn('meeting_type');
        $mockConstraintViolationMeetingType
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->andReturn($mockConstraintViolationMeetingType);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'meeting_type'        => '',
                'meeting_type_initials' => 'PA',
            ]);

        $returnedErrors = $meetingTypeService->createMeetingType($mockRequest);
        $this->assertEquals('meeting_type: This value should not be blank.', $returnedErrors[0]);
    }

    public function testCreateRegionWithMissingMeetingTypeInitials()
    {
        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockConstraintViolationMeetingTypeInitials = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingTypeInitials
            ->shouldReceive('getPropertyPath')
            ->andReturn('meeting_type_initials');
        $mockConstraintViolationMeetingTypeInitials
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->andReturn($mockConstraintViolationMeetingTypeInitials);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'meeting_type'        => 'Psychological Abuse',
                'meeting_type_initials' => '',
            ]);

        $returnedErrors = $meetingTypeService->createMeetingType($mockRequest);
        $this->assertEquals('meeting_type_initials: This value should not be blank.', $returnedErrors[0]);
    }

    public function testEditMeetingType()
    {
        $mockRegion = Mockery::mock(MeetingType::class);
        $mockRegion->shouldReceive('setMeetingType')->andReturnSelf();
        $mockRegion->shouldReceive('setMeetingTypeInitials')->andReturnSelf();

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

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'meeting_type'        => 'Behavioral Abuse',
                'meeting_type_initials' => 'BA',
            ]);

        $meetingTypeService->editMeetingType($mockRequest);
    }

    public function testEditRegionMissingRegionAndRegionAbbrev()
    {

        $mockConstraintViolationMeetingType = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingType
            ->shouldReceive('getPropertyPath')
            ->andReturn('meetingType');
        $mockConstraintViolationMeetingType
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');

        $mockConstraintViolationMeetingTypeInitials = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingTypeInitials
            ->shouldReceive('getPropertyPath')
            ->andReturn('meetingTypeInitials');
        $mockConstraintViolationMeetingTypeInitials
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
            ->andReturn($mockConstraintViolationMeetingType);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationMeetingTypeInitials);

        $mockMeetingType = Mockery::mock(MeetingType::class);
        $mockMeetingType->shouldReceive('setMeetingType')->andReturnSelf();
        $mockMeetingType->shouldReceive('setMeetingTypeInitials')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockMeetingType);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'meeting_type'        => '',
                'meeting_type_initials' => '',
            ]);

        $returnedErrors = $meetingTypeService->editMeetingType($mockRequest);
        $this->assertEquals('meetingType: This value should not be blank.', $returnedErrors[0]);
        $this->assertEquals('meetingTypeInitials: This value should not be blank.', $returnedErrors[1]);
    }

    public function testEditMeetingTypeMissingMeetingType()
    {

        $mockConstraintViolationMeetingType = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingType
            ->shouldReceive('getPropertyPath')
            ->andReturn('meetingType');
        $mockConstraintViolationMeetingType
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');


        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationMeetingType);

        $mockMeetingType = Mockery::mock(MeetingType::class);
        $mockMeetingType->shouldReceive('setMeetingType')->andReturnSelf();
        $mockMeetingType->shouldReceive('setMeetingTypeInitials')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockMeetingType);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'meeting_type'        => '',
                'meeting_type_initials' => 'RA',
            ]);

        $returnedErrors = $meetingTypeService->editMeetingType($mockRequest);
        $this->assertEquals('meetingType: This value should not be blank.', $returnedErrors[0]);
    }

    public function testEditRegionMissingRegionAbbrev()
    {

        $mockConstraintViolationMeetingTypeInitials = Mockery::mock(ConstraintViolation::class);
        $mockConstraintViolationMeetingTypeInitials
            ->shouldReceive('getPropertyPath')
            ->andReturn('meetingTypeInitials');
        $mockConstraintViolationMeetingTypeInitials
            ->shouldReceive('getMessage')
            ->andReturn('This value should not be blank.');


        $mockConstraintViolationList = Mockery::mock(ConstraintViolationList::class);

        $mockConstraintViolationList
            ->shouldReceive('count')
            ->andReturn(1);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationMeetingTypeInitials);

        $mockConstraintViolationList
            ->shouldReceive('get')
            ->once()
            ->ordered()
            ->andReturn($mockConstraintViolationMeetingTypeInitials);

        $mockMeetingType = Mockery::mock(MeetingType::class);
        $mockMeetingType->shouldReceive('setMeetingType')->andReturnSelf();
        $mockMeetingType->shouldReceive('setMeetingTypeInitials')->andReturnSelf();

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockMeetingType);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validate')
            ->andReturn($mockConstraintViolationList);

        $em = Mockery::mock(EntityManager::class);

        $em->shouldReceive('persist')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest          = Mockery::mock(Request::class);
        $mockRequest->request = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->request
            ->shouldReceive('all')
            ->andReturn([
                'id'            => 1,
                'meeting_type'        => 'Behavioral Abuse',
                'meeting_type_initials' => '',
            ]);

        $returnedErrors = $meetingTypeService->editMeetingType($mockRequest);
        $this->assertEquals('meetingTypeInitials: This value should not be blank.', $returnedErrors[0]);
    }

    public function testDeleteMeetingType()
    {

        $mockEntityRepository = Mockery::mock(EntityRepository::class);

        $mockRegion = Mockery::mock(MeetingType::class);

        $mockEntityRepository
            ->shouldReceive('findOneBy')
            ->andReturn($mockRegion);

        $validator = Mockery::mock(ValidatorInterface::class);

        $em = Mockery::mock(EntityManager::class);
        $em->shouldReceive('remove')
           ->andReturnSelf();
        $em->shouldReceive('flush')
           ->andReturnSelf();

        $meetingTypeService = new MeetingTypeService($em, $mockEntityRepository, $validator);

        $mockRequest        = Mockery::mock(Request::class);
        $mockRequest->query = Mockery::mock(ParameterBag::class);

        $mockRequest
            ->query
            ->shouldReceive('all')
            ->andReturn([
                'id' => 1,
            ]);

        $meetingTypeService->deleteMeetingType($mockRequest);
    }
}
