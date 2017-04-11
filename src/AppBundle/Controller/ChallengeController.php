<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MeetingType;
use AppBundle\Entity\Region;
use AppBundle\Form\MeetingFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChallengeController extends Controller
{

    /**
     * @Route("/challenge/")
     * @codeCoverageIgnore
     */
    public function indexAction()
    {

        $meetingTypeRepo = $this->getDoctrine()->getRepository(MeetingType::class);
        $regionRepo = $this->getDoctrine()->getRepository(Region::class);

        $allMeetingTypes = $meetingTypeRepo->findAll();
        $allRegions = $regionRepo->findAll();

        $form = $this->createForm(MeetingFilterType::class, [$allMeetingTypes, $allRegions], array(
            'action' => $this->generateUrl('/challenge/meetingsFromLocation'),
            'method' => 'POST'
        ))->createView();

        return $this->render('default/address_input.html.twig', [
            'page_title'    => 'Request Meeting Times, Types and Locations',
            'form'          => $form
        ]);
    }

    /**
     * @Route("/challenge/meetingsFromLocation", name="/challenge/meetingsFromLocation")
     */
    public function meetingsFromLocationAction(Request $request)
    {
        $treatmentCenterService = $this->get('app.treatment_center_service');
        $meetingInformation     = $treatmentCenterService->getTreatmentCenters($request);

        $response = $this->render('default/meeting.html.twig', [
            'meetingInformation' => $meetingInformation,
        ]);

        return $response;
    }
}