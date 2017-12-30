<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\BankBranch;
use Rbs\Bundle\CoreBundle\Form\Type\BankBranchForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * BankBranch controller.
 *
 * @Route("/settings/bank-branch")
 */
class BankBranchController extends Controller
{

    /**
     * Lists all BankBranch entities.
     *
     * @Route("", name="bankbranch")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.bank_branch');
        $datatable->buildDatatable();
        return array(
            'datatable' => $datatable,
        );
    }

    /**
     * @Route("/list_ajax", name="bank_branch_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.bank_branch');
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
     * Creates a new BankBranch entity.
     *
     * @Route("/create", name="bankbranch_create")
     * @Template("RbsCoreBundle:BankBranch:new.html.twig")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function createAction(Request $request)
    {
        $entity = new BankBranch();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('bankbranch');
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a BankBranch entity.
     *
     * @param BankBranch $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BankBranch $entity)
    {
        $form = $this->createForm(new BankBranchForm(), $entity, array(
            'action' => $this->generateUrl('bankbranch_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }


    /**
    * Creates a form to edit a BankBranch entity.
    *
    * @param BankBranch $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BankBranch $entity)
    {
        $form = $this->createForm(new BankBranchForm(), $entity, array(
            'action' => $this->generateUrl('bankbranch_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BankBranch entity.
     *
     * @Route("/{id}/edit", name="bankbranch_update", options={"expose"=true})
     * @Template("RbsCoreBundle:BankBranch:new.html.twig")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:BankBranch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BankBranch entity.');
        }

        $form = $this->createEditForm($entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('bankbranch');
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Deletes a BankBranch entity.
     *
     * @Route("/{id}", name="bankbranch_delete")
     * @Method("DELETE")
     * @JMS\Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:BankBranch')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BankBranch entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('settings_bankbranch'));
    }

    /**
     * Creates a form to delete a BankBranch entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('settings_bankbranch_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
