<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChallengeController extends Controller {

	/**
	* @Route("/challenge/")
	*/
	public function indexAction()
	{

        return $this->render('default/address_input.html.twig', array(
            'page_title' => 'Request Meeting Times, Types and Locations',
            'states' => $this->container->getParameter('states'),
            'days' => $this->container->getParameter('days'),
            'meeting_types' => $this->container->getParameter('meeting_types')
        ));
	}
	/**
	* @Route("/challenge/meetingsFromLocation")
	*/
	public function meetingsFromLocationAction(Request $request)
	{

        $treatmentCenter = $this->get('app.treatment_center_service');
        $meetingInformation = $treatmentCenter->getTreatmentCenters($request);
        return $this->render('default/meeting.html.twig', array(
            'meetingInformation' => $meetingInformation,
        ));
	}
}