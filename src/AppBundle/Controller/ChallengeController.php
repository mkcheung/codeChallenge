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
        $memcache = $this->get('memcache.default');
        $cacheKey = $this->assembleCacheKey($request);

        $meetingInformation = $memcache->get($cacheKey);
        
        if (empty($meetingInformation)) {

            $treatmentCenterService = $this->get('app.treatment_center_service');
            $meetingInformation     = $treatmentCenterService->getTreatmentCenters($request);
            $memcache->set($cacheKey, $meetingInformation, 0, 3600);
        }

        $response = $this->render('default/meeting.html.twig', [
            'meetingInformation' => $meetingInformation,
        ]);

        return $response;
    }

    private function assembleCacheKey(Request $request){

        $inputParameters = $request->request->all();

        if (empty($inputParameters)) {
            $inputParameters = $request->query->all();
        }
        $cacheKey = '';

        $cacheKey .= $inputParameters['street_address'];
        $cacheKey .= $inputParameters['city'];
        $cacheKey .= $inputParameters['state'];
        $cacheKey .= $inputParameters['zip_code'];
        $cacheKey .= $inputParameters['day'];

        if (!empty($inputParameters['meeting_type'])) {
            $meetingTypes = $inputParameters['meeting_type'];
            foreach ($meetingTypes as $meetingType) {
                $cacheKey .= $meetingType;
            }
        }

        if(empty($cacheKey)){
            return 'default';
        }

        return $cacheKey;
    }
}