<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\Project;
use Rbs\Bundle\CoreBundle\Form\Type\ProjectForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Project controller.
 *
 * @Route("/factory")
 */
class ProjectController extends BaseController
{
    /**
     * @Route("", name="project")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.project');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/project_list_ajax", name="project_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.project');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("core_projects.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("RbsCoreBundle:Project:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function createAction(Request $request)
    {
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->flashMessage('success', 'Factory Created Successfully');
            return $this->redirect($this->generateUrl('project'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @param Project $entity The entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectForm(), $entity, array(
            'action' => $this->generateUrl('project_create'),
            'method' => 'POST',
            'attr' => array('novalidate' => 'novalidate')
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function newAction()
    {
        $entity = new Project();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="project_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Factory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="project_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Factory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * @param Project $entity The entity
    * @return \Symfony\Component\Form\Form The form
    * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
    */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectForm(), $entity, array(
            'action' => $this->generateUrl('project_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('novalidate' => 'novalidate')
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}", name="project_update")
     * @Method("PUT")
     * @Template("RbsCoreBundle:Project:edit.html.twig")
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Factory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->flashMessage('success', 'Factory Updated Successfully');
            return $this->redirect($this->generateUrl('project'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}", name="project_delete", options={"expose"=true})
     * @Method("DELETE")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Factory entity.');
            }
            $this->flashMessage('success', 'Factory Deleted Successfully');
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_PROJECT_MANAGE")
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete-form')))
            ->setAction($this->generateUrl('project_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}