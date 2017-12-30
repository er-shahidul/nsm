<?php

namespace Rbs\Bundle\CoreBundle\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

/**
 * Class TransportCommissionHistoryDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class TransportCommissionHistoryDatatable extends BaseDatatable
{
    /** @var Request */
    protected $request;
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $securityToken,
        Twig_Environment $twig,
        TranslatorInterface $translator,
        RouterInterface $router,
        EntityManagerInterface $em,
        ContainerInterface $container
    )
    {
        $template = array(
            'base' => 'SgDatatablesBundle:Datatable:datatable.html.twig',
            'html' => 'SgDatatablesBundle:Datatable:datatable_html.html.twig',
            'js' => 'SgDatatablesBundle:Datatable:datatable_js.html.twig',
        );
        parent::__construct($authorizationChecker, $securityToken, $twig, $translator, $router, $em, $template);

        $this->request = $container->get('request');
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $districtId = $this->request->get('id');
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('transport_commission_history_list_ajax', array('id' => $districtId)),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('updatedAt', 'datetime', array('title' => 'Date', 'date_format' => 'LLL' ))
            ->add('updatedBy.username', 'column', array('title' => 'CreatedBy'))
            ->add('station.name', 'column', array('title' => 'Station'))
            ->add('depo.name', 'column', array('title' => 'Depo'))
            ->add('amount', 'column', array('title' => 'Amount'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\CoreBundle\Entity\TransportIncentive';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'transport_incentive_history_datatable';
    }
}
