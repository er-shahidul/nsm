<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\BankAccount;
use Rbs\Bundle\CoreBundle\Form\Type\BankAccountForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * BankAccount controller.
 *
 * @Route("/settings/bank-account")
 */
class BankAccountController extends Controller
{

    /**
     * Lists all BankAccount entities.
     *
     * @Route("", name="bank_account")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.bank_account');
        $datatable->buildDatatable();
        return array(
            'datatable' => $datatable,
        );
    }

    /**
     * @Route("/list_ajax", name="bank_account_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.bank_account');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new BankAccount entity.
     *
     * @Route("/create", name="bank_account_create")
     * @Template("RbsCoreBundle:BankAccount:new.html.twig")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function createAction(Request $request)
    {
        $entity = new BankAccount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bank_account'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a BankAccount entity.
     *
     * @param BankAccount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BankAccount $entity)
    {
        $form = $this->createForm(new BankAccountForm($this->getDoctrine()->getManager(), $this->getBankBranchList()), $entity, array(
            'action' => $this->generateUrl('bank_account_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
    * Creates a form to edit a BankAccount entity.
    *
    * @param BankAccount $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BankAccount $entity)
    {
        $form = $this->createForm(new BankAccountForm($this->getDoctrine()->getManager(), $this->getBankBranchList()), $entity, array(
            'action' => $this->generateUrl('bank_account_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing BankAccount entity.
     *
     * @Route("/{id}/edit", name="bank_account_update", options={"expose"=true})
     * @Template("RbsCoreBundle:BankAccount:new.html.twig")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:BankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BankAccount entity.');
        }

        $form = $this->createEditForm($entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('bank_account');
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * Deletes a BankAccount entity.
     *
     * @Route("/{id}", name="bank_account_delete")
     * @Method("DELETE")
     * @JMS\Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:BankAccount')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BankAccount entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('settings_bank-account'));
    }

    /**
     * Creates a form to delete a BankAccount entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('settings_bank-account_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    private function getBankBranchList()
    {
        return $this->getDoctrine()->getRepository('RbsCoreBundle:BankBranch')->getBranchListWithBankName();
    }
}
