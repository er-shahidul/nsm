<?php

namespace Rbs\Bundle\SalesBundle\Controller\Report;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\SalesBundle\Form\Search\Type\AgentSearchType;
use Rbs\Bundle\SalesBundle\Form\Search\Type\DistrictItemMonthSearchType;
use Rbs\Bundle\SalesBundle\Form\Search\Type\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Report Controller.
 *
 */
class PaymentReportController extends Controller
{

    /**
     * @Route("/report/payment", name="report_payment")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALES_REPORT")
     */
    public function districtWiseItemMonthlyReportAction(Request $request)
    {
        $agents = $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->getAgentListKeyValue();
        $form = new AgentSearchType($agents);
        $data = $request->query->get($form->getName());
        $formSearch = $this->createForm($form, $data);

        if ($request->query->get('search[start_date]', null, true)){
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        }
        $agent = $data['agent'] ? $this->getDoctrine()->getRepository('RbsSalesBundle:Agent')->find($data['agent']) : null;

        $data = $this->getDoctrine()->getRepository('RbsSalesBundle:Payment')->getPaymentReportData($request->query->get('type'), $data['start_date'], $data['end_date'], $agent);

        return $this->render('RbsSalesBundle:Report:payment.html.twig', array(
            'formSearch' => $formSearch->createView(),
            'data'       => $data,
            'type'       => $request->query->get('type'),
            'agent'      => $agent
        ));
    }
}