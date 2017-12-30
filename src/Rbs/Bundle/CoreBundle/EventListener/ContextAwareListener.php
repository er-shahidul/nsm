<?php
namespace Rbs\Bundle\CoreBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class ContextAwareListener
{
    /** @var AuthorizationChecker */
    protected $authorizationChecker;

    /** @var Request */
    protected $request;

    private $route;

    protected $user;

    /**
     * @param AuthorizationChecker $context
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->authorizationChecker = $container->get('security.authorization_checker');
        $this->request = $container->get('request');
        $this->route = $this->request->get('_route');
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
    }

    protected function isMatch($pattern)
    {
        return strpos($this->route, $pattern) === 0;
    }
}