<?php


namespace Vallam\Framework;


use Symfony\Component\Routing\RouteCollection;
use Vallam\Engine\AbstractVallamSingleton;

class Router extends AbstractVallamSingleton
{
    protected RouteCollection $routerCollection;

    public function __boot()
    {
        $this->routerCollection = new RouteCollection();
    }

    public function getRouteCollection() : RouteCollection
    {
        return $this->routerCollection;
    }
}