<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/13/17
 * Time: 2:01 PM
 */
namespace AppBundle\Controller;

use AppBundle\Form\MeetingType\MeetingTypeCreate;
use AppBundle\Form\MeetingType\MeetingTypeEdit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\MeetingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MeetingTypeController extends Controller
{

    /**
     * @Route("/meetingType/", name="/meetingType/")
     * @codeCoverageIgnore
     */
    public function indexAction()
    {

        $meetingTypeRepo = $this->getDoctrine()->getRepository(MeetingType::class);
        $meetingTypes = $meetingTypeRepo->findAll();

        return $this->render('AppBundle:MeetingType:meeting_type.html.twig', [
            'page_title'    => 'Meeting Types',
            'meetingTypes' => $meetingTypes,
        ]);
    }

    /**
     * @Route("/meetingType/create", name="/meetingType/create")
     * @codeCoverageIgnore
     */
    public function createAction(Request $request)
    {

        if($request->isMethod('POST')){

            $meetingTypeService = $this->get('app.meeting_type_service');
            $meetingTypeService->createMeetingType($request);

            return $this->redirect($this->generateUrl('homepage'));
        }

        $form = $this->createForm(MeetingTypeCreate::class, null, [
            'action' => $this->generateUrl("/meetingType/create"),
            'method' => 'POST',
        ])->createView();

        return $this->render('AppBundle:MeetingType:create_meeting_type.html.twig', [
            'page_title'    => 'Create Meeting Type:',
            'form' => $form,
        ]);
    }

    /**
     * @Route("/meetingType/edit", name="/meetingType/edit")
     * @codeCoverageIgnore
     */
    public function editAction(Request $request)
    {

        if($request->isMethod('POST')){

            $meetingTypeService = $this->get('app.meeting_type_service');
            $meetingTypeService->editMeetingType($request);

            return $this->redirect($this->generateUrl('homepage'));
        }

        $editParameters = $request->query->all();
        $meetingTypeRepo = $this->getDoctrine()->getRepository(MeetingType::class);
        $meetingType = $meetingTypeRepo->findOneBy(['meeting_type_id' => $editParameters['id']]);

        $form = $this->createForm(MeetingTypeEdit::class, $meetingType, [
            'action' => $this->generateUrl('/meetingType/edit'),
            'method' => 'POST',
        ])->createView();

        return $this->render('AppBundle:MeetingType:edit_meeting_type.html.twig', [
            'page_title'    => 'Edit Meeting Type:',
            'form' => $form,
        ]);
    }

    /**
     * @Route("/meetingType/delete", name="/meetingType/delete")
     * @codeCoverageIgnore
     */
    public function deleteAction(Request $request)
    {
        $regionService = $this->get('app.meeting_type_service');
        $regionService->deleteMeetingType($request);

        return $this->redirect($this->generateUrl('homepage'));
    }
}