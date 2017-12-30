<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Form\Type\CategoryForm;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\Category;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends BaseController
{
    /**
     * @Route("", name="category")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.category');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/category_list_ajax", name="category_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.category');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("core_categories.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/", name="category_create")
     * @Method("POST")
     * @Template("RbsCoreBundle:Category:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->flashMessage('success', 'Category Created Successfully');
            return $this->redirect($this->generateUrl('category'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @param Category $entity The entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    private function createCreateForm(Category $entity)
    {
        $form = $this->createForm(new CategoryForm(), $entity, array(
            'action' => $this->generateUrl('category_create'),
            'method' => 'POST',
            'attr' => array(
                'novalidate' => 'novalidate'
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="category_new")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="category_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
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
    * @param Category $entity The entity
    * @return \Symfony\Component\Form\Form The form
    * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
    */
    private function createEditForm(Category $entity)
    {
        $form = $this->createForm(new CategoryForm(), $entity, array(
            'action' => $this->generateUrl('category_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'novalidate' => 'novalidate'
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}", name="category_update")
     * @Method("PUT")
     * @Template("RbsCoreBundle:Category:edit.html.twig")
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->flashMessage('success', 'Category Updated Successfully');
            return $this->redirect($this->generateUrl('category'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}", name="category_delete", options={"expose"=true})
     * @Method("DELETE")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $this->flashMessage('success', 'Category Deleted Successfully');
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    /**
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_CATEGORY_MANAGE")
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete-form')))
            ->setAction($this->generateUrl('category_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
