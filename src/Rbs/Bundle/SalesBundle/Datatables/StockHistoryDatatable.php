<?php

namespace Rbs\Bundle\SalesBundle\Datatables;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

/**
 * Class StockHistoryDatatable
 *
 * @package Rbs\Bundle\SalesBundle\Datatables
 */
class StockHistoryDatatable extends BaseDatatable
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
        $stockId = $this->request->get('stock');
        $this->features->setFeatures($this->defaultFeatures());
        $this->options->setOptions($this->defaultOptions());
        $this->options->setOptions(array_merge($this->defaultOptions(), array(
            'order' => [[0, 'desc']],
        )));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('stock_history_list_ajax', array('stock' => $stockId)),
            'type' => 'GET'
        ));

        $this->columnBuilder
            ->add('createdAt', 'datetime', array(
                'title' => 'Date',
                'date_format' => 'LL' // default = "lll"
            ))
            ->add('createdBy.username', 'column', array('title' => 'CreatedBy'))
            ->add('stock.depo.name', 'column', array('title' => 'Depo'))
            ->add('quantity', 'column', array('title' => 'Quantity'))
            ->add('description', 'column', array('title' => 'Description'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Rbs\Bundle\SalesBundle\Entity\StockHistory';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'stock_datatable';
    }
}
