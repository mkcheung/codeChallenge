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

        return $this->render('region/region.html.twig', [
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

            $this->redirectToRoute('/challenge/');
        }

        $form = $this->createForm(RegionType\RegionCreate::class, null, [
            'action' => $this->generateUrl('/region/create'),
            'method' => 'POST',
        ])->createView();

        return $this->render('region/create_region.html.twig', [
            'page_title'    => 'Create Meeting Type:',
            'form' => $form,
        ]);
    }
}