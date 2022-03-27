<?php


namespace Vallam;


use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Framework implements HttpKernelInterface
{
    private UrlMatcher $matcher;
    private ControllerResolver $controllerResolver;
    private ArgumentResolver $argumentResolver;
    private EventDispatcher $eventDispatcher;
    private HttpKernel $kernel;

    protected RequestStack $requestStack;

    public function __construct(UrlMatcher $matcher, ControllerResolver $controllerResolver, ArgumentResolver $argumentResolver)
    {
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->eventDispatcher = Hull::getInstance()->getEventDispatcher();
    }

    private function createRequestStack()
    {
        $this->requestStack = new RequestStack();
    }

    public function handle(Request $request, $type = HttpKernelInterface::MAIN_REQUEST, $catch = true) : Response
    {
        $this->createRequestStack();

        $this->eventDispatcher->addSubscriber(
            new RouterListener(
                $this->matcher, $this->requestStack
            )
        );

        $this->kernel = new HttpKernel(
            $this->eventDispatcher,
            $this->controllerResolver,
            $this->requestStack,
            $this->argumentResolver
        );

        return $this->kernel->handle($request);
    }

    public function getKernel()
    {
        return $this->kernel;
    }
}