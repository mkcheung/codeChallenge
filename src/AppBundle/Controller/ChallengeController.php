<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MeetingType;
use AppBundle\Entity\Region;
use AppBundle\Service\TreatmentCenterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\MeetingFilterType\MeetingFilterType as MeetingFilterForm;

class ChallengeController extends Controller
{

    /**
     * @Route("/challenge/", name="/challenge/")
     * @codeCoverageIgnore
     */
    public function indexAction()
    {

        $meetingTypeRepo = $this->getDoctrine()->getRepository(MeetingType::class);
        $regionRepo      = $this->getDoctrine()->getRepository(Region::class);

        $allMeetingTypes = $meetingTypeRepo->findAll();
        $allRegions      = $regionRepo->findAll();

        $form = $this->createForm(MeetingFilterForm::class, [$allMeetingTypes, $allRegions], [
            'action' => $this->generateUrl('/challenge/meetingsFromLocation'),
            'method' => 'POST',
        ])->createView();

        return $this->render('AppBundle:Challenge:address_input.html.twig', [
            'page_title' => 'Request Meeting Times, Types and Locations',
            'form'       => $form,
        ]);
    }

    /**
     * @Route("/challenge/meetingsFromLocation", name="/challenge/meetingsFromLocation")
     */
    public function meetingsFromLocationAction(Request $request)
    {
        /** @var TreatmentCenterService $treatmentCenterService */
        $treatmentCenterService = $this->get('app.treatment_center_service');
        $memcache               = $this->get('memcache.default');
        $cacheKey               = $treatmentCenterService->assembleCacheKey($request);

        $meetingInformation = $memcache->get($cacheKey);

        if (empty($meetingInformation)) {

            $meetingInformation = $treatmentCenterService->getTreatmentCenters($request);
            $memcache->set($cacheKey, $meetingInformation, 0, 3600);
        }

        $response = $this->render('AppBundle:Challenge:meeting.html.twig', [
            'meetingInformation' => $meetingInformation,
        ]);

        return $response;
    }
}