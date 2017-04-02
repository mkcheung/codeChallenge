<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JsonRPC\Client;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;

class ChallengeController extends Controller {

	/**
	* @Route("/challenge/")
	*/
	public function indexAction()
	{
        $treatmentCenter = $this->get('app.treatment_center');
        $results = $treatmentCenter->getTreatmentCenters();
        // var_dump($results);die;
        return $this->render('default/index.html.twig', array(
            'page_title' => 'Monday Treatment Meetings From RB',
            'results' => $results,
        ));
	}
}