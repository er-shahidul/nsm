<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\Vendor;
use Rbs\Bundle\CoreBundle\Form\Type\VendorForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Vendor controller.
 *
 * @Route("/vendor")
 */
class VendorController extends Controller
{
    /**
     * @Route("", name="vendor")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.vendor');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/vendor_list_ajax", name="vendor_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.vendor');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("core_vendors.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/", name="vendor_create")
     * @Method("POST")
     * @Template("RbsCoreBundle:Vendor:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function createAction(Request $request)
    {
        $entity = new Vendor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Vendor Created Successfully');
            return $this->redirect($this->generateUrl('vendor'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @param Vendor $entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    private function createCreateForm(Vendor $entity)
    {
        $form = $this->createForm(new VendorForm(), $entity, array(
            'action' => $this->generateUrl('vendor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="vendor_new")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function newAction()
    {
        $entity = new Vendor();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="vendor_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="vendor_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
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
     * @param Vendor $entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    private function createEditForm(Vendor $entity)
    {
        $form = $this->createForm(new VendorForm(), $entity, array(
            'action' => $this->generateUrl('vendor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}", name="vendor_update")
     * @Method("PUT")
     * @Template("RbsCoreBundle:Vendor:edit.html.twig")
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vendor Updated Successfully');
            return $this->redirect($this->generateUrl('vendor'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}", name="vendor_delete", options={"expose"=true})
     * @Method("DELETE")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:Vendor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vendor entity.');
            }
            $this->addFlash('success', 'Vendor Deleted Successfully');
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('vendor'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_VENDOR_MANAGE")
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete-form')))
            ->setAction($this->generateUrl('vendor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
