<?php
namespace Vallam;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Registry;

class Logger
{
    private \Monolog\Logger $logger;

    public function __construct()
    {
        $this->logger = new \Monolog\Logger(DEFAULT_LOG_CHANNEL);
        $this->createFrameworkLog();
        //Registry::addLogger($this->logger);
    }

    public function cloneLogHandler(string $name) : \Monolog\Logger
    {
        return $this->logger->withName($name);
    }

    public function attachHandler(HandlerInterface $handler)
    {
        $this->logger->pushHandler($handler);
        return $this;
    }

    private function createFrameworkLog()
    {
        $this->attachHandler(new StreamHandler(Hull::getInstance()->getStoragePath()));
    }

    public function debug(string $message)
    {
        $this->logger->debug( $message);
    }

    public function getLogger()
    {
        return $this->logger;
    }
}