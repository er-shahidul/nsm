<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Rbs\Bundle\CoreBundle\Event\ItemEvent;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\Item;
use Rbs\Bundle\CoreBundle\Form\Type\ItemForm;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Item controller.
 *
 * @Route("/item")
 */
class ItemController extends BaseController
{
    /**
     * @Route("", name="item")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.item');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/item_list_ajax", name="item_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.item');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);
        /** @var QueryBuilder $qb */
        $function = function($qb)
        {
            $qb->andWhere("core_items.deletedAt IS NULL");
        };
        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * @Route("/new", name="item_create")
     * @Method("POST")
     * @Template("RbsCoreBundle:Item:new.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function createAction(Request $request)
    {
        $entity = new Item();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->flashMessage('success', 'Item Created Successfully');

            $this->dispatch('core.item.created', new ItemEvent($entity));

            return $this->redirect($this->generateUrl('item'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @param Item $entity The entity
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    private function createCreateForm(Item $entity)
    {
        $form = $this->createForm(new ItemForm(), $entity, array(
            'action' => $this->generateUrl('item_create'),
            'method' => 'POST',
            'attr' => array(
                'novalidate' => 'novalidate'
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="item_new")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function newAction()
    {
        $entity = new Item();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="item_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="item_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
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
    * @param Item $entity The entity
    * @return \Symfony\Component\Form\Form The form
    * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
    */
    private function createEditForm(Item $entity)
    {
        $form = $this->createForm(new ItemForm(), $entity, array(
            'action' => $this->generateUrl('item_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'novalidate' => 'novalidate'
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}/edit", name="item_update")
     * @Method("PUT")
     * @Template("RbsCoreBundle:Item:edit.html.twig")
     * @param Request $request
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->flashMessage('success', 'Item Updated Successfully');

            return $this->redirect($this->generateUrl('item'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}", name="item_delete", options={"expose"=true})
     * @Method("DELETE")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:Item')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Item entity.');
            }
            $this->flashMessage('success', 'Item Deleted Successfully');
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('item'));
    }

    /**
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('item_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/status-change/{id}", name="item_statue_change", options={"expose"=true})
     * @Method("GET")
     * @param Item $item
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_ITEM_MANAGE")
     */
    public function statusChangeAction(Item $item)
    {
        if ($item->getStatus() === 1) {
            $item->setStatus(0);
            $this->flashMessage('success', 'Item Disabled Successfully');
        } else {
            $item->setStatus(1);
            $this->flashMessage('success', 'Item Enabled Successfully');
        }

        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('item'));
    }

    /**
     * @Route("find_item_ajax", name="find_item_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function findItemAction(Request $request)
    {
        $itemId = $request->request->get('item');

        $item = $this->getDoctrine()->getRepository('RbsCoreBundle:Item')->find($itemId);

        $response = new Response(json_encode(array(
            'price' => $item->getPrice()
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{id}/set-price", name="set_item_price", options={"expose"=true})
     * @param Request $request
     * @param Item $item
     * @return Response
     */
    public function setItemPriceAction(Request $request, Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $locations = $em->getRepository('RbsCoreBundle:Location')
            ->findBy(array('level' => 4), array('name' => 'ASC'));
        $itemPrices = $em->getRepository('RbsCoreBundle:ItemPrice')->getCurrentPriceAsArray($item);

        if ($request->isMethod('POST')) {
            $em->getRepository('RbsCoreBundle:ItemPrice')->save($request->request->all(), $locations, $item, $itemPrices);
        }

        return $this->render('RbsCoreBundle:Item:set-price.html.twig', array(
            'item' => $item,
            'locations' => $locations,
            'itemPrices' => $itemPrices,
        ));
    }
}
