<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Event\DepoEvent;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\Depo;
use Rbs\Bundle\CoreBundle\Form\Type\DepoForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Depo controller.
 *
 * @Route("/depo")
 */
class DepoController extends BaseController
{
    /**
     * @Route("", name="depo")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.depo');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/depo_list_ajax", name="depo_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.depo');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("core_depos.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/new", name="depo_create")
     * @Method("POST")
     * @Template("RbsCoreBundle:Depo:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Depo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->getRepository('RbsCoreBundle:Depo')->create($entity);
            $this->dispatch('core.depo.created', new DepoEvent($entity));
            $this->flashMessage('success', 'Depo Created Successfully');
            return $this->redirect($this->generateUrl('depo'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Depo $entity The entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    private function createCreateForm(Depo $entity)
    {
        $form = $this->createForm(new DepoForm(), $entity, array(
            'action' => $this->generateUrl('depo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="depo_new")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function newAction()
    {
        $entity = new Depo();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="depo_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Depo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Depo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="depo_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Depo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Depo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @param Depo $entity The entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    private function createEditForm(Depo $entity)
    {
        $form = $this->createForm(new DepoForm(), $entity, array(
            'action' => $this->generateUrl('depo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}", name="depo_update")
     * @Method("PUT")
     * @Template("RbsCoreBundle:Depo:edit.html.twig")
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Depo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Depo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->flashMessage('success', 'Depo Updated Successfully');
            return $this->redirect($this->generateUrl('depo'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}", name="depo_delete", options={"expose"=true})
     * @Method("DELETE")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:Depo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Depo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('depo'));
    }

    /**
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_DEPO_MANAGE")
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete-form')))
            ->setAction($this->generateUrl('depo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }
}
