<?php

namespace Dothiv\APIBundle\Transformer;

use Symfony\Component\Routing\RouterInterface;

abstract class AbstractTransformer
{

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $route;

    /**
     * @param RouterInterface $router
     * @param string          $route
     */
    public function __construct(
        RouterInterface $router,
        $route
    )
    {
        $this->router = $router;
        $this->route  = $route;
    }
}
