<?php
namespace Vallam\Engine\ServiceLayer;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractServiceLayer
{
    protected string $name;
    protected string $type;
    protected Request $request;
    protected string $output = '';
    protected bool $isSessionEnabled;
    protected string $incomingRequest;

    public function __construct()
    {
        $this->handle();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    protected function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function appendOutput(string $output)
    {
        $this->output .= $output;
        return $this;
    }

    public function setOutput(string $output)
    {
        $this->output = $output;
        return $this;
    }

    public function clearOutput()
    {
        $this->output = '';
        return $this;
    }

    protected function getOutput() : string
    {
        return $this->output;
    }

    public function isSession() : bool
    {
        return $this->isSessionEnabled;
    }

    public function setRequestResourceURI($requestURI)
    {
        $this->incomingRequest = $requestURI;
        return $this;
    }

    abstract public function response();

    abstract protected function handle();
}