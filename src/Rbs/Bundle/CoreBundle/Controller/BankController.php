<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rbs\Bundle\CoreBundle\Entity\Bank;
use Rbs\Bundle\CoreBundle\Form\Type\BankForm;
use JMS\SecurityExtraBundle\Annotation as JMS;

/**
 * Bank controller.
 *
 * @Route("/settings/bank")
 */
class BankController extends Controller
{

    /**
     * Lists all Bank entities.
     *
     * @Route("", name="bank")
     * @Method("GET")
     * @Template()
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function indexAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.bank');
        $datatable->buildDatatable();
        $deleteForm = $this->createDeleteForm(0);
        return array(
            'datatable' => $datatable,
            'deleteForm' => $deleteForm->createView()
        );
    }

    /**
     * @Route("/list_ajax", name="bank_list_ajax", options={"expose"=true})
     * @Method("GET")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function listAjaxAction()
    {
        $datatable = $this->get('rbs_erp.core.datatable.bank');
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
     * Creates a new Bank entity.
     *
     * @Route("/create", name="bank_create")
     * @Template("RbsCoreBundle:Bank:new.html.twig")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function createAction(Request $request)
    {
        $entity = new Bank();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('bank');
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Bank entity.
     *
     * @param Bank $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Bank $entity)
    {
        $form = $this->createForm(new BankForm(), $entity, array(
            'action' => $this->generateUrl('bank_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
    * Creates a form to edit a Bank entity.
    *
    * @param Bank $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Bank $entity)
    {
        $form = $this->createForm(new BankForm(), $entity, array(
            'action' => $this->generateUrl('bank_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Bank entity.
     *
     * @Route("/{id}/edit", name="bank_update", options={"expose"=true})
     * @Template("RbsCoreBundle:Bank:new.html.twig")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RbsCoreBundle:Bank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bank entity.');
        }

        $form = $this->createEditForm($entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('bank');
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Deletes a Bank entity.
     *
     * @Route("/{id}", name="bank_delete")
     * @Method("DELETE")
     * @JMS\Secure(roles="ROLE_HEAD_OFFICE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RbsCoreBundle:Bank')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Bank entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bank'));
    }

    /**
     * Creates a form to delete a Bank entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bank_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
