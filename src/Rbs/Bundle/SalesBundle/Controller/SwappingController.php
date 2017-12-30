<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Rbs\Bundle\CoreBundle\Entity\Location;
use Rbs\Bundle\SalesBundle\Form\Type\SwappingRsmForm;
use Rbs\Bundle\SalesBundle\Form\Type\SwappingSrForm;
use Rbs\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Swapping Controller.
 *
 */
class SwappingController extends Controller
{
    /**
     * @Route("/swapping/rsm/list", name="swapping_rsm_list", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SWAPPING_MANAGE")
     */
    public function swappingRsmListAction()
    {
        $rsmList = $this->em()->getRepository('RbsUserBundle:User')->getRsmList();

        return $this->render('RbsSalesBundle:Swapping:rsm_list.html.twig', array(
            'rsmList' => $rsmList
        ));
    }

    /**
     * @Route("/swapping/rsm/create/{id}", name="swapping_rsm_create")
     * @Template("RbsSalesBundle:Swapping:swapping_rsm_form.html.twig")
     * @param Request $request
     * @param User $user
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SWAPPING_MANAGE")
     */
    public function swappingRsmCreateAction(Request $request, User $user)
    {
        $form = $this->createForm(new SwappingRsmForm($user), $user);

        if ('POST' === $request->getMethod()) {
            $user->setZilla($this->em()->getRepository('RbsCoreBundle:Location')->find($request->request->get('rsm_swap')['areaNew']));
            $this->em()->getRepository('RbsUserBundle:User')->update($user);

            $userSwapping = $this->em()->getRepository('RbsUserBundle:User')->find($request->request->get('rsm_swap')['userChange']);
            $userSwapping->setZilla($this->em()->getRepository('RbsCoreBundle:Location')->find($request->request->get('rsm_swap')['areaOld']));
            $this->em()->getRepository('RbsUserBundle:User')->update($userSwapping);
            $this->get('session')->getFlashBag()->add('success', 'Swap Successfully!');

            return $this->redirect($this->generateUrl('swapping_rsm_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/get_users_by_location/{id}", name="get_users_by_location", options={"expose"=true})
     * @param Location $location
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SWAPPING_MANAGE")
     */
    public function getUsersByLocation(Location $location)
    {
        $users = $this->em()->getRepository('RbsUserBundle:User')->findUsersByLocation($location->getId());

        $usersArr = array();
        foreach ($users as $user) {
            $usersArr[] = array('id' => $user->getId(), 'text' => $user->getUsername());
        }

        return new JsonResponse($usersArr);
    }

    /**
     * @Route("/swapping/sr/list", name="swapping_sr_list", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SWAPPING_MANAGE")
     */
    public function swappingSrListAction()
    {
        $srList = $this->em()->getRepository('RbsUserBundle:User')->getSrList();
        
        return $this->render('RbsSalesBundle:Swapping:sr_list.html.twig', array(
            'srList' => $srList
        ));
    }

    /**
     * @Route("/swapping/sr/create/{id}", name="swapping_sr_create")
     * @Template("RbsSalesBundle:Swapping:swapping_sr_form.html.twig")
     * @param Request $request
     * @param User $user
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_SWAPPING_MANAGE")
     */
    public function swappingSrCreateAction(Request $request, User $user)
    {
        $form = $this->createForm(new SwappingSrForm($user), $user);

        if ('POST' === $request->getMethod()) {
            $user->setZilla($this->em()->getRepository('RbsCoreBundle:Location')->find($request->request->get('sr_swap')['location']));
            $this->em()->getRepository('RbsUserBundle:User')->update($user);
            $this->get('session')->getFlashBag()->add('success', 'Swap Successfully!');

            return $this->redirect($this->generateUrl('swapping_sr_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function em()
    {
        return $this->getDoctrine()->getManager();
    }
}