<?php
namespace Rbs\Bundle\UserBundle\Service\Datatable;


use Sg\DatatablesBundle\Datatable\View\DatatableViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Serializer;

class DatatableDataManager
{
    /**
     * The request.
     *
     * @var Request
     */
    private $request;

    /**
     * The serializer service.
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Configuration settings.
     *
     * @var array
     */
    private $configs;

    //-------------------------------------------------
    // Ctor.
    //-------------------------------------------------

    /**
     * Ctor.
     *
     * @param RequestStack $requestStack
     * @param Serializer $serializer
     * @param array $configs
     */
    public function __construct(RequestStack $requestStack, Serializer $serializer, array $configs)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->serializer = $serializer;
        $this->configs = $configs;
    }

    //-------------------------------------------------
    // Public
    //-------------------------------------------------

    /**
     * Get query.
     *
     * @param DatatableViewInterface $datatableView
     *
     * @return DatatableQuery
     */
    public function getQueryFrom(DatatableViewInterface $datatableView)
    {
        $type = $datatableView->getAjax()->getType();
        $parameterBag = null;

        if ('GET' === strtoupper($type)) {
            $parameterBag = $this->request->query;
        }

        if ('POST' === strtoupper($type)) {
            $parameterBag = $this->request->request;
        }

        $params = $parameterBag->all();
        $query = new DatatableQuery($this->serializer, $params, $datatableView, $this->configs);

        return $query;
    }
}