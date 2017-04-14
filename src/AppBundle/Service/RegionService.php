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
    private $regionRepository;

    public function __construct(
        EntityManager $entityManager,
        EntityRepository $regionRepository
    ) {
        $this->em                    = $entityManager;
        $this->regionRepository = $regionRepository;
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

        $deleteParameters = $request->query->all();
        $region = $this->regionRepository->findOneBy(['region_id' => $deleteParameters['id']]);

        $this->em->remove($region);
        $this->em->flush();
    }
}