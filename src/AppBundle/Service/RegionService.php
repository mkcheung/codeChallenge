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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use AppBundle\Service\Traits\ValidatorTrait;

class RegionService
{

    use ValidatorTrait;

    private $em;
    private $regionRepository;

    public function __construct(
        EntityManager $entityManager,
        EntityRepository $regionRepository,
        ValidatorInterface $validator
    ) {
        $this->em               = $entityManager;
        $this->regionRepository = $regionRepository;
        $this->validator        = $validator;
    }

    public function createRegion(
        Request $request
    ) {
        $inputParameters  = $request->request->all();
        $region           = new Region($inputParameters['region'], $inputParameters['region_abbrev']);

        $errors = $this->validateEntity($region);

        if(count($errors) > 0){
            return $errors;
        }

        $this->em->persist($region);
        $this->em->flush();
    }

    public function editRegion(
        Request $request
    ) {

        $editParameters = $request->request->all();
        $region         = $this->regionRepository->findOneBy(['region_id' => $editParameters['id']]);
        $region->setRegion($editParameters['region']);
        $region->setRegionAbbrev($editParameters['region_abbrev']);

        $errors = $this->validateEntity($region);

        if(count($errors) > 0){
            return $errors;
        }

        $this->em->persist($region);
        $this->em->flush();
    }

    public function deleteRegion(
        Request $request
    ) {

        $deleteParameters = $request->query->all();
        $region           = $this->regionRepository->findOneBy(['region_id' => $deleteParameters['id']]);

        $this->em->remove($region);
        $this->em->flush();
    }

}