<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use JMS\SecurityExtraBundle\Annotation as JMS;
use Rbs\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Location controller.
 *
 * @Route("/location")
 */
class LocationController extends BaseController
{
    /**
     * @Route("/filter-location/", name="location_filter", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function areaFilterAction(Request $request)
    {
        $locations = array();
        $zillaId = $request->query->get('id');
        $upozillaId = $request->query->get('upozila_id');
        if ($zillaId) {
            $locations = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->findBy(
                array(
                    'parentId' => $zillaId,
                )
            );
        }

        $html = '<option value="">Choose an option</option>';
        foreach ($locations as $r) {
            $selected = $upozillaId == $r->getId() ? ' selected="selected"' : '';
            $html .= '<option value="' . $r->getId() . '" ' . $selected . '>' . $r->getName() . '</option>';
        }

        return new Response($html);
    }

    /**
     * @Route("/filter-location-update/{id}", name="location_filter_update", options={"expose"=true})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function areaUpdateFilterAction(Request $request, User $user)
    {
        $html = '';
        $locations = array();
        if ($request->query->get('id')) {
            $locations = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->findBy(
                array(
                    'parentId' => $request->query->get('id')
                )
            );
        }

        if($user->getUpozilla() == true) {
            $html = '<option value="">Choose an option</option>';
            foreach ($locations as $r) {
                if ($r->getId() == $user->getUpozilla()->getId()) {
                    $selected = "selected=\"selected\"";
                } else {
                    $selected = '';
                }
                $html .= '<option value="' . $r->getId() . '" ' . $selected . '>' . $r->getName() . '</option>';
            }
        }

        return new Response($html);
    }

    /**
     * @Route("", name="locations")
     * @Template("RbsCoreBundle:Location:index.html.twig")
     * @return array
     * @JMS\Secure(roles="ROLE_LOCATION_MANAGE")
     */
    public function indexAction()
    {
        $locations = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getAllFromSectorToUpozilla();

        $locationsSectors = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getSectors();
        $locationsZones = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getZones();
        $locationsRegions = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getRegions();
        $locationsDistricts = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getDistricts();
        $locationsUpozillas = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->getUpozillas();

        return array(
            'locations' => $locations,
            'locationsSectors' => $locationsSectors,
            'locationsZones' => $locationsZones,
            'locationsRegions' => $locationsRegions,
            'locationsDistricts' => $locationsDistricts,
            'locationsUpozillas' => $locationsUpozillas
        );
    }

}
