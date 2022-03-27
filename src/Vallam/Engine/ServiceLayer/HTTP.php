<?php

namespace Vallam\Engine\ServiceLayer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Vallam\Engine\ServiceLayer\HTTP\Event\ResponseEvent;
use Vallam\Controller\TestController;
use Vallam\Engine\ServiceLayer\HTTP\Listener\ContentLengthListener;
use Vallam\Engine\ServiceLayer\HTTP\Listener\ControllerResponseListener;
use Vallam\Framework;
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

    protected ControllerResolver $controllerResolver;

    protected ArgumentResolver $argumentResolver;

    protected RequestStack $requestStack;

    protected HttpKernelInterface $framework;

    public function __construct(RequestContext $requestContext)
    {
        $this->eventDispatcher = Hull::getInstance()->getEventDispatcher();;
        Router::createInstance();
        $this->setRequest(Request::createFromGlobals());
        $this->requestContext = $requestContext;
        parent::__construct();
    }

    public function response()
    {
        $response = $this->framework->handle($this->getRequest());

        $response->send();

        $this->eventDispatcher->dispatch(new ResponseEvent($response, $this->getRequest()), 'response');

        $this->framework->getKernel()->terminate($this->getRequest(), $response);
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

        $this->controllerResolver = new ControllerResolver(Hull::getInstance()->getLogger());
        $this->argumentResolver = new ArgumentResolver();

        $controller = $this->controllerResolver->getController($this->getRequest());
        $this->argumentResolver->getArguments($this->getRequest(), $controller);

        $this->framework = new Framework($this->urlMatcher, $this->controllerResolver, $this->argumentResolver);

        $this->cache();

        $this->response();
    }

    protected function registerEvents()
    {
        $this->eventDispatcher->addSubscriber(new ControllerResponseListener());
        $this->eventDispatcher->addSubscriber(new ContentLengthListener());
        $this->eventDispatcher->addSubscriber(new ErrorListener('Vallam\Controller\ErrorController::exception'));
    }

    private function cache()
    {
        $this->framework = new HttpCache(
            $this->framework,
            new Store(sprintf('%s/%s', Hull::getInstance()->getBasePath(), VLM_HTTP_CACHE))
        );
    }
}