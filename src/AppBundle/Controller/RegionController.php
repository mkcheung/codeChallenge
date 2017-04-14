<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/13/17
 * Time: 2:01 PM
 */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\RegionType ;
use AppBundle\Entity\Region;
use AppBundle\Form\RegionType\RegionEdit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegionController extends Controller
{

    /**
     * @Route("/region/", name="/region/")
     * @codeCoverageIgnore
     */
    public function indexAction()
    {

        $regionRepo = $this->getDoctrine()->getRepository(Region::class);
        $regions = $regionRepo->findAll();

        return $this->render('AppBundle:Region:region.html.twig', [
            'page_title'    => 'Regions',
            'regions' => $regions,
        ]);
    }

    /**
     * @Route("/region/create", name="/region/create")
     * @codeCoverageIgnore
     */
    public function createAction(Request $request)
    {

        if($request->isMethod('POST')){

            $regionService = $this->get('app.region_service');
            $regionService->createRegion($request);

            return $this->redirect($this->generateUrl('homepage'));
        }

        $form = $this->createForm(RegionType\RegionCreate::class, null, [
            'action' => $this->generateUrl('/region/create'),
            'method' => 'POST',
        ])->createView();

        return $this->render('AppBundle:Region:create_region.html.twig', [
            'page_title'    => 'Create Region:',
            'form' => $form,
        ]);
    }

    /**
     * @Route("/region/edit", name="/region/edit")
     * @codeCoverageIgnore
     */
    public function editAction(Request $request)
    {

        if($request->isMethod('POST')){

            $regionService = $this->get('app.region_service');
            $regionService->editRegion($request);

            return $this->redirect($this->generateUrl('homepage'));
        }

        $editParameters = $request->query->all();
        $regionRepo = $this->getDoctrine()->getRepository(Region::class);
        $region = $regionRepo->findOneBy(['region_id' => $editParameters['id']]);

        $form = $this->createForm(RegionEdit::class, $region, [
            'action' => $this->generateUrl('/region/edit'),
            'method' => 'POST',
        ])->createView();

        return $this->render('AppBundle:Region:edit_region.html.twig', [
            'page_title'    => 'Edit Region:',
            'region' => $region,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/region/delete", name="/region/delete")
     * @codeCoverageIgnore
     */
    public function deleteAction(Request $request)
    {
        $regionService = $this->get('app.region_service');
        $regionService->deleteRegion($request);

        return $this->redirect($this->generateUrl('homepage'));
    }
}