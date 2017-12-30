<?php

namespace Rbs\Bundle\SalesBundle\Controller\Report;

use Rbs\Bundle\SalesBundle\Form\Search\Type\DepoItemSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Stock Report Controller.
 *
 */
class StockReportController extends Controller
{

    /**
     * @Route("/report/stock", name="report_stock")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_SALES_REPORT")
     */
    public function StockReportAction(Request $request)
    {
        $form = new DepoItemSearchType();
        $data = $request->query->get($form->getName());
        $formSearch = $this->createForm($form, $data);
        $formSearch->submit($data);
        $stacks = $this->getDoctrine()->getRepository('RbsSalesBundle:Stock')->getAllStacks($data);

        return $this->render('RbsSalesBundle:Report:stock.html.twig', array(
            'formSearch' => $formSearch->createView(),
            'data'       => $data,
            'stacks'     => $stacks
        ));
    }
}