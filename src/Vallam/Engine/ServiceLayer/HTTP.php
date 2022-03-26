<?php

namespace Vallam\Engine\ServiceLayer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Vallam\Controller\TestController;
use Vallam\Framework\Router;
use Vallam\Hull;

class HTTP extends AbstractServiceLayer
{
    protected string $name = DEFAULT_HTTP_SERVICE;

    protected string $type = HTTP_SERVICE_TYPE;

    protected Router $router;

    protected RequestContext $requestContext;

    protected UrlMatcher $urlMatcher;

    protected EventDispatcher $eventDispatcher;

    protected HttpKernel $kernel;

    protected ControllerResolver $controllerResolver;

    protected ArgumentResolver $argumentResolver;

    protected RequestStack $requestStack;

    public function __construct(RequestContext $requestContext)
    {
        Router::createInstance();
        $this->setRequest(Request::createFromGlobals());
        $this->requestContext = $requestContext;
        parent::__construct();
    }

    public function response()
    {
        echo $this->getOutput();
    }

    private function createRequestStack()
    {
        $this->requestStack = new RequestStack();
    }

    private function loadRoutes()
    {
        $router = Router::getInstance();
        $route = $router->getRouteCollection();

        $route->add('rocket', new Route('/rocket', ['_controller' => TestController::class, '_action' => 'index']));
    }

    protected function handle()
    {
        $this->loadRoutes();

        $this->urlMatcher = new UrlMatcher(
            Router::getInstance()->getRouteCollection(),
            $this->requestContext
        );

        $routeAttributes = $this->urlMatcher->match($this->request->getPathInfo());
        if(($routeAttributes['_controller'] ?? false) && ($routeAttributes['_action'] ?? false)) {
            $routeAttributes['_controller'] = sprintf('%s::%s', $routeAttributes['_controller'], $routeAttributes['_action']);
        }

        $this->request->attributes->add($routeAttributes);

        $this->createRequestStack();

        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber(
            new RouterListener(
                $this->urlMatcher, $this->requestStack
            )
        );

        $this->controllerResolver = new ControllerResolver(Hull::getInstance()->getLogger());
        $this->argumentResolver = new ArgumentResolver();

        $controller = $this->controllerResolver->getController($this->getRequest());
        $this->argumentResolver->getArguments($this->getRequest(), $controller);

        $this->kernel = new HttpKernel(
            $this->eventDispatcher,
            $this->controllerResolver,
            $this->requestStack,
            $this->argumentResolver
        );

        $response = $this->kernel->handle($this->getRequest());
        $response->send();

        $this->kernel->terminate($this->getRequest(), $response);
    }
}