<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\SubCategory;
use Rbs\Bundle\CoreBundle\Form\Type\SubCategoryForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * SubCategory controller.
 *
 * @Route("/subcategory")
 */
class SubCategoryController extends BaseController
{
    /**
     * @Route("", name="subcategory")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.sub_category');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/sub_category_list_ajax", name="sub_category_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.sub_category');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("core_sub_categories.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/", name="subcategory_create")
     * @Method("POST")
     * @Template("RbsCoreBundle:SubCategory:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function createAction(Request $request)
    {
        $entity = new SubCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subcategory_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @param SubCategory $entity The entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    private function createCreateForm(SubCategory $entity)
    {
        $form = $this->createForm(new SubCategoryForm(), $entity, array(
            'action' => $this->generateUrl('subcategory_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="subcategory_new")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function newAction()
    {
        $entity = new SubCategory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="subcategory_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:SubCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SubCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="subcategory_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:SubCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SubCategory entity.');
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
    * @param SubCategory $entity The entity
    * @return \Symfony\Component\Form\Form The form
    * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
    */
    private function createEditForm(SubCategory $entity)
    {
        $form = $this->createForm(new SubCategoryForm(), $entity, array(
            'action' => $this->generateUrl('subcategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}", name="subcategory_update")
     * @Method("PUT")
     * @Template("RbsCoreBundle:SubCategory:edit.html.twig")
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:SubCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SubCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('subcategory_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}", name="subcategory_delete", options={"expose"=true})
     * @Method("DELETE")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:SubCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SubCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('subcategory'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_SUB_CATEGORY_MANAGE")
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete-form')))
            ->setAction($this->generateUrl('subcategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
