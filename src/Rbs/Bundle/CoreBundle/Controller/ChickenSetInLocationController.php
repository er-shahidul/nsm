<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use JMS\SecurityExtraBundle\Annotation as JMS;
use Rbs\Bundle\CoreBundle\Entity\ChickenSet;
use Rbs\Bundle\CoreBundle\Form\Type\ChickenSetForLocationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * ChickenSetInLocation controller.
 *
 */
class ChickenSetInLocationController extends BaseController
{
    /**
     * @Route("/chicken/set/in/location", name="chicken_set_in_location")
     * @Template("RbsCoreBundle:Chicken:index.html.twig")
     * @return array
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function indexAction()
    {
        $chickenTypeSetArr = $this->getDoctrine()->getRepository('RbsCoreBundle:ChickenSet')->getAll();

        return array(
            'chickenTypeSetArr' => $chickenTypeSetArr,
        );
    }

    /**
     * @Route("/chicken/set/in/location/add/{id}", name="chicken_set_in_location_add")
     * @Template("RbsCoreBundle:Chicken:add.html.twig")
     * @param ChickenSet $chickenSet
     * @return array
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function addAction(ChickenSet $chickenSet)
    {
        return array(
            'chickenSet' => $chickenSet
        );
    }

    /**
     * @Route("/chicken/set/in/location/save/{id}", name="chicken_set_in_location_save")
     * @param Request $request
     * @param ChickenSet $chickenSet
     * @return array
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function saveAction(Request $request, ChickenSet $chickenSet)
    {
        if($request->request->get('quantity') == null){
            $this->flashMessage('error', 'Quantity should not be blank');
            return $this->redirect($this->generateUrl('chicken_set_in_location'));
        }elseif(!is_numeric($request->request->get('quantity'))){
            $this->flashMessage('error', 'Quantity should be number');
            return $this->redirect($this->generateUrl('chicken_set_in_location'));
        }
        $chickenSet->setQuantity($request->request->get('quantity'));
        $this->getDoctrine()->getRepository('RbsCoreBundle:ChickenSet')->update($chickenSet);
        $this->flashMessage('success', 'Chicken Set Assigned Successfully');
        return $this->redirect($this->generateUrl('chicken_set_in_location'));
    }

    /**
     * @Route("/chicken/set/in/location/create", name="chicken_set_in_location_create")
     * @Template("RbsCoreBundle:Chicken:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function agentBankInfoCreateAction(Request $request)
    {
        $chickenSet = new ChickenSet();
        $form = $this->createForm(new ChickenSetForLocationForm(), $chickenSet, array(
            'action' => $this->generateUrl('chicken_set_in_location_create'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($request->request->get('sale_chicken_set')['item'] == null) {
                $this->flashMessage('error', 'Item Should Not Be Blank');

                return $this->redirect($this->generateUrl('chicken_set_in_location'));
            }
            if ($form->isValid()) {
                $locations = $this->getDoctrine()->getRepository('RbsCoreBundle:Location')->findBy(
                    array(
                        'level' => 4,
                    )
                );
                $itemCheck = $this->getDoctrine()->getManager()->getRepository('RbsCoreBundle:ChickenSet')->findOneBy(
                    array(
                        'item' => $request->request->get('sale_chicken_set')['item'],
                    )
                );
                if ($itemCheck == null) {
                    foreach ($locations as $location) {
                        $chickenSet = new ChickenSet();
                        $chickenSet->setLocation($location);
                        $chickenSet->setStatus(1);
                        $chickenSet->setItem(
                            $this->getDoctrine()->getManager()->getRepository('RbsCoreBundle:Item')->find(
                                $request->request->get('sale_chicken_set')['item']
                            )
                        );
                        $this->getDoctrine()->getManager()->getRepository('RbsCoreBundle:ChickenSet')->create(
                            $chickenSet
                        );
                    }
                } else {
                    $this->flashMessage('success', 'This Item Is Already Added!');

                    return $this->redirect($this->generateUrl('chicken_set_in_location'));
                }
                $this->flashMessage('success', 'Location Wise Chicken Set Assigned Successfully');

                return $this->redirect($this->generateUrl('chicken_set_in_location'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }
}
