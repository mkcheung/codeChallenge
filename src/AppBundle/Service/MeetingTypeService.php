<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/13/17
 * Time: 1:52 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\MeetingType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Service\Traits\ValidatorTrait;


class MeetingTypeService
{

    use ValidatorTrait;

    private $em;
    private $meetingTypeRepository;

    public function __construct(
        EntityManager $entityManager,
        EntityRepository $meetingTypeRepository,
        ValidatorInterface $validator
    ) {
        $this->em                    = $entityManager;
        $this->meetingTypeRepository = $meetingTypeRepository;
        $this->validator        = $validator;

    }

    public function createMeetingType(
        Request $request
    ) {

        $inputParameters = $request->request->all();
        $meetingType = new MeetingType($inputParameters['meeting_type'], $inputParameters['meeting_type_initials']);

        $errors = $this->validateEntity($meetingType);

        if(count($errors) > 0){
            return $errors;
        }

        $this->em->persist($meetingType);
        $this->em->flush();
    }

    public function editMeetingType(
        Request $request
    ) {

        $editParameters = $request->request->all();
        $meetingType = $this->meetingTypeRepository->findOneBy(['meeting_type_id' => $editParameters['id']]);
        $meetingType->setMeetingType($editParameters['meeting_type']);
        $meetingType->setMeetingTypeInitials($editParameters['meeting_type_initials']);

        $errors = $this->validateEntity($meetingType);

        if(count($errors) > 0){
            return $errors;
        }

        $this->em->persist($meetingType);
        $this->em->flush();
    }

    public function deleteMeetingType(
        Request $request
    ) {

        $deleteParameters = $request->query->all();
        $meetingType = $this->meetingTypeRepository->findOneBy(['meeting_type_id' => $deleteParameters['id']]);

        $this->em->remove($meetingType);
        $this->em->flush();
    }
}