<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Rbs\Bundle\SalesBundle\Entity\Sms;
use Rbs\Bundle\SalesBundle\Helper\ChickOrderSmsParser;
use Rbs\Bundle\SalesBundle\Helper\SmsParse;
use Rbs\Bundle\SalesBundle\Helper\SmsVehicleParse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="homepage", options={"expose"=true})
     * @Template()
     */
    public function indexAction()
    {
        $creditLimitNotification = $this->getDoctrine()->getRepository('RbsSalesBundle:CreditLimit')->creditLimitNotificationCount();
        
        return array(
            'creditLimitNotification' => $creditLimitNotification
        );
    }

    /**
     * @Route("/order-generate", name="order_via_sms")
     * @Template("RbsCoreBundle:Default:order-generate.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index2Action(Request $request)
    {
        $data = array();
        $form = $this->createFormBuilder($data);
        $form->add('mobile', 'text', array(
            'required' => true,
            'constraints' => array(
                new NotBlank(array(
                    'message'=>'Please Fill Out'
                ))
            )
        ));
        $form->add('msg', 'textarea', array(
            'required' => true,
            'constraints' => array(
                new NotBlank(array(
                    'message'=>'Please Fill Out'
                ))
            )
        ));
        $form->add('type', 'choice', array(
            'choices' => array(
                'feed' => 'Feed',
                'chick' => 'Chick',
            )
        ));

        $form->add('Submit', 'submit');

        $formView = $form->getForm();
        if ($request->isMethod('POST')) {
            $formView->handleRequest($request);
            if ($formView->isValid()) {

                $orderType = $formView->get('type')->getData();
                if ($orderType == 'chick') {
                    $smsParse = new ChickOrderSmsParser($this->get('doctrine.orm.entity_manager'));
                } else {
                    $smsParse = new SmsParse($this->get('doctrine.orm.entity_manager'), $this->container, $formView->get('mobile')->getData());
                }

                $sms = new Sms();
                $sms->setMobileNo($formView->get('mobile')->getData());
                $sms->setMsg($formView->get('msg')->getData());
                $sms->setDate(new \DateTime());
                $sms->setSl(rand());
                $sms->setStatus('NEW');
                $response = $smsParse->parse($sms);

                if ($response) {
                    $this->flashMessage('success', 'Order Created Successfully, Order ID: ' . $response['orderId']);
                } else {
                    $this->flashMessage('error', $smsParse->error);

                }
                return $this->redirectToRoute('order_via_sms');
            }
        }

        return array(
            'form' => $formView->createView()
        );

    }

    /**
     * @Route("/vehicle-generate", name="vehicle_via_sms")
     * @Template("RbsCoreBundle:Default:vehicle-generate.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index3Action(Request $request)
    {
        $data = array();
        $form = $this->createFormBuilder($data);
        $form->add('mobile', 'text', array(
            'required' => true,
            'constraints' => array(
                new NotBlank(array(
                    'message'=>'Please Fill Out'
                ))
            )
        ));
        $form->add('msg', 'textarea', array(
            'required' => true,
            'constraints' => array(
                new NotBlank(array(
                    'message'=>'Please Fill Out'
                ))
            )
        ));
        $form->add('Submit', 'submit');

        $formView = $form->getForm();
        if ($request->isMethod('POST')) {
            $formView->handleRequest($request);
            if ($formView->isValid()) {
                $user = $this->getDoctrine()->getRepository('RbsUserBundle:User')->findByPhoneNumber($formView->get('mobile')->getData());
                if ($user == null) {
                    $this->flashMessage('error', 'Invalid Phone Number');
                } else {
                    $smsVehicleParse = new SmsVehicleParse($this->getDoctrine()->getManager(), $user[0]);
                    $response = $smsVehicleParse->parse($formView->get('msg')->getData());

                    if ($response) {
                        $this->flashMessage('success', 'Vehicle Created Successfully');
                    } else {
                        $this->flashMessage('error', $smsVehicleParse->error);
                    }
                }
                return $this->redirectToRoute('vehicle_via_sms');
            }
        }

        return array(
            'form' => $formView->createView()
        );

    }

}
