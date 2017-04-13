<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/13/17
 * Time: 1:50 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\Region;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;


class RegionService
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

    public function createRegion(
        Request $request
    ) {

        $inputParameters = $request->request->all();
        $region = new Region($inputParameters['region'], $inputParameters['region_initials']);

        $this->em->persist($region);
        $this->em->flush();
    }

    public function editRegion(
        Request $request
    ) {

    }

    public function deleteRegion(
        Request $request
    ) {

    }
}