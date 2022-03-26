<?php

namespace Vallam\Engine\ServiceLayer;

class Console extends AbstractServiceLayer
{
    protected string $name = DEFAULT_CONSOLE_SERVICE;

    protected string $type = CONSOLE_SERVICE_TYPE;

    protected bool $isSessionEnabled = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function response()
    {
        // TODO: Implement response() method.
    }

    protected function handle()
    {
        // TODO: Implement handle() method.
    }
}