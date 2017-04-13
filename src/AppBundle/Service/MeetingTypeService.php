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


class MeetingTypeService
{

    private $em;
    private $meetingTypeRepository;

    public function __construct(
        EntityManager $entityManager,
        EntityRepository $meetingTypeRepository
    ) {
        $this->em                    = $entityManager;
        $this->meetingTypeRepository = $meetingTypeRepository;

    }

    public function createMeetingType(
        Request $request
    ) {

        $inputParameters = $request->request->all();
        $meetingType = new MeetingType($inputParameters['meeting_type'], $inputParameters['meeting_type_initials']);

        $this->em->persist($meetingType);
        $this->em->flush();

    }

    public function editMeetingType(
        Request $request
    ) {

    }

    public function deleteMeetingType(
        Request $request
    ) {

    }
}