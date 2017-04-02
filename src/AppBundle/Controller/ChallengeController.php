<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChallengeController extends Controller {

	/**
	* @Route("/challenge/")
	*/
	public function indexAction()
	{

        $treatmentCenter = $this->get('app.treatment_center');
        $results = $treatmentCenter->getTreatmentCenters();
        return $this->render('default/index.html.twig', array(
            'page_title' => 'Monday Treatment Meetings From RB',
            'results' => $results,
        ));
	}
}