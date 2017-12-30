<?php

namespace Rbs\Bundle\SalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Entity\Location;
use Rbs\Bundle\SalesBundle\Entity\DamageGood;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Form\Type\DamageGoodForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * DamageGood Controller.
 *
 */
class DamageGoodController extends BaseController
{
    /**
     * @Route("/damage-good/list", name="damage_good_list")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_SR_GROUP")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.damage.good');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:DamageGood:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/damage_good_list_ajax", name="damage_good_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_SR_GROUP")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.damage.good');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->join('sales_damage_goods.agent', 'a');
            $qb->join('sales_damage_goods.user', 'u');
            $qb->andWhere('u =:user');
            $qb->orderBy('sales_damage_goods.createdAt', 'DESC');
            $qb->setParameter('user', $this->getUser());
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/damage-good/create", name="damage_good_form", options={"expose"=true})
     * @Template("RbsSalesBundle:DamageGood:form.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @JMS\Secure(roles="ROLE_SR_GROUP")
     */
    public function damageGoodCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $damageGood = new DamageGood();
        /** @var Location $location */
        $location = $this->getUser()->getZilla();

        $form = $this->createForm(new DamageGoodForm($location, $em), $damageGood, array(
            'action' => $this->generateUrl('damage_good_form'), 'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $damageGood->setUser($this->getUser());
                $od = $em->getRepository('RbsSalesBundle:Order')->find($request->request->get('damage_good')['orderRef']);
                $damageGood->setAgent($od->getAgent());
                $this->getDoctrine()->getManager()->getRepository('RbsSalesBundle:DamageGood')->create($damageGood);
                $this->flashMessage('success', 'Damage Goods add Successfully!');
                return $this->redirect($this->generateUrl('damage_good_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/damage-good/admin/list", name="damage_good_admin_list")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_DAMAGE_GOODS_VERIFY, ROLE_DAMAGE_GOODS_APPROVE")
     */
    public function indexAdminAction()
    {
        $datatable = $this->get('rbs_erp.sales.datatable.damage.good.admin');
        $datatable->buildDatatable();

        return $this->render('RbsSalesBundle:DamageGood:index.html.twig', array(
            'datatable' => $datatable
        ));
    }

    /**
     * @Route("/damage_good_admin_list_ajax", name="damage_good_admin_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_DAMAGE_GOODS_VERIFY, ROLE_DAMAGE_GOODS_APPROVE")
     */
    public function listAjaxAdminAction()
    {
        $user = $this->getUser();
        $datatable = $this->get('rbs_erp.sales.datatable.damage.good.admin');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function ($qb) use ($user){
            $qb->join('sales_damage_goods.agent', 'a');
            $qb->join('sales_damage_goods.user', 'u');
            if($user->hasRole("ROLE_DAMAGE_GOODS_VERIFY")) {
                $qb->andWhere('sales_damage_goods.status = :ACTIVE');
                $qb->setParameter('ACTIVE', DamageGood::ACTIVE);
            }elseif($user->hasRole("ROLE_DAMAGE_GOODS_APPROVE")){
                $qb->andWhere('sales_damage_goods.status = :ACTIVE');
                $qb->setParameter('ACTIVE', DamageGood::VERIFIED);
            }
            $qb->orderBy('sales_damage_goods.createdAt', 'DESC');
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/damage-good/verify/{id}", name="damage_goods_verify", options={"expose"=true})
     * @param DamageGood $damageGood
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DAMAGE_GOODS_VERIFY")
     */
    public function verifyAction(DamageGood $damageGood)
    {
        $damageGood->setStatus(DamageGood::VERIFIED);
        $damageGood->setVerifiedAt(new \DateTime());
        $damageGood->setVerifiedBy($this->getUser());
        $this->getDoctrine()->getRepository('RbsSalesBundle:DamageGood')->update($damageGood);

        $this->get('session')->getFlashBag()->add(
            'success',
            'Damage Goods Successfully Verified'
        );

        return $this->redirect($this->generateUrl('damage_good_admin_list'));
    }

    /**
     * @Route("/damage-good/reject-modal/{id}", name="damage_goods_reject_form", options={"expose"=true})
     * @param DamageGood $damageGood
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_DAMAGE_GOODS_VERIFY")
     */
    public function rejectModalAction(DamageGood $damageGood)
    {
        return $this->render('RbsSalesBundle:DamageGood:_rejectForm.html.twig', array(
            'id' => $damageGood->getId()
        ));
    }

    /**
     * @Route("/damage-good/reject/{id}", name="damage_goods_reject", options={"expose"=true})
     * @param DamageGood $damageGood
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DAMAGE_GOODS_VERIFY")
     */
    public function verifyRejectAction(Request $request, DamageGood $damageGood)
    {
        $damageGood->setRejectReason($request->request->get('reason'));
        $damageGood->setStatus(DamageGood::REJECTED);
        $damageGood->setVerifiedAt(new \DateTime());
        $damageGood->setVerifiedBy($this->getUser());
        $this->getDoctrine()->getRepository('RbsSalesBundle:DamageGood')->update($damageGood);

        $this->get('session')->getFlashBag()->add(
            'success',
            'Damage Goods Rejected'
        );

        return $this->redirect($this->generateUrl('damage_good_admin_list'));
    }

    /**
     * @Route("/damage-good/approve/{id}", name="damage_goods_approve", options={"expose"=true})
     * @param Request $request
     * @param DamageGood $damageGood
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DAMAGE_GOODS_APPROVE")
     */
    public function approveAction(Request $request, DamageGood $damageGood)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = new Payment();
        $payment->setAgent($damageGood->getAgent());
        $payment->setAmount($request->request->get('refund'));
        $payment->setPaymentMethod(Payment::PAYMENT_METHOD_REFUND);
        $payment->setRemark('Refund for damage goods.');
        $payment->setDepositDate(date("Y-m-d"));
        $payment->setTransactionType(Payment::CR);
        $payment->setVerified(true);
        $payment->addOrder($damageGood->getOrderRef());

        $damageGood->setRefundAmount($request->request->get('refund'));
        $damageGood->setStatus(DamageGood::APPROVED);
        $damageGood->setApprovedAt(new \DateTime());
        $damageGood->setApprovedBy($this->getUser());
        $this->getDoctrine()->getRepository('RbsSalesBundle:DamageGood')->update($damageGood);

        $em->getRepository('RbsSalesBundle:Order')->orderAmountAdjust($payment);
        $em->getRepository('RbsSalesBundle:Payment')->create($payment);

        $this->get('session')->getFlashBag()->add(
            'success',
            'Damage Goods Successfully Verified'
        );

        return $this->redirect($this->generateUrl('damage_good_admin_list'));
    }

    /**
     * @Route("/damage-good/view/{id}", name="damage_goods_view", options={"expose"=true})
     * @param DamageGood $damageGood
     * @return \Symfony\Component\HttpFoundation\Response
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER, ROLE_SR_GROUP, ROLE_DAMAGE_GOODS_VERIFY, ROLE_DAMAGE_GOODS_APPROVE")
     */
    public function viewAction(DamageGood $damageGood)
    {
        return $this->render('RbsSalesBundle:DamageGood:view.html.twig', array(
            'damageGood' => $damageGood
        ));
    }
}