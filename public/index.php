<?php

use Symfony\Component\Routing\RequestContext;
use Vallam\Engine\ServiceLayer\HTTP;
use Vallam\Hull;

define("VLM_INDEX_PATH", __DIR__);
require __DIR__ . '/../vendor/autoload.php';

$hull = Hull::createInstance();

$hull->setIndexPath(__DIR__)
    ->setVendorPath(__DIR__ . '/../vendor')
    ->setBasePath(__DIR__ . '/..')
    ->startLogger()
    ->startEventDispatcher()
    ->setHTTPServiceLayer(HTTP::class, new RequestContext(), $_SERVER['REQUEST_URI'])
    ->boot();