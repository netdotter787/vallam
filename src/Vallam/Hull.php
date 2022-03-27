<?php
namespace Vallam;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\RequestContext;
use Vallam\Configurations\Manager;
use Vallam\Engine\AbstractVallamSingleton;
use Vallam\Engine\ServiceLayer\AbstractServiceLayer;

class Hull extends AbstractVallamSingleton
{
    private AbstractServiceLayer $serviceLayer;
    private Logger $logger;
    private string $indexPath;
    private string $basePath;
    private string $vendorPath;
    /**
     * @var EventDispatcher
     */
    private EventDispatcher $eventDispatcher;

    /**
     * @var ContainerBuilder
     */
    private ContainerBuilder $container;

    /**
     * @return string
     */
    public function getIndexPath(): string
    {
        return $this->indexPath;
    }

    /**
     * @param string $indexPath
     * @return Hull
     */
    public function setIndexPath(string $indexPath): Hull
    {
        $this->indexPath = $indexPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     * @return Hull
     */
    public function setBasePath(string $basePath): Hull
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }

    /**
     * @param string $vendorPath
     * @return Hull
     */
    public function setVendorPath(string $vendorPath): Hull
    {
        $this->vendorPath = $vendorPath;
        return $this;
    }

    public function getServiceLayerInstance() : AbstractServiceLayer
    {
        return $this->serviceLayer;
    }

    public function getStoragePath()
    {
        return $logFilePath = sprintf('%s/%s', $this->getBasePath(), DEFAULT_LOG_PATH);
    }

    protected function __boot()
    {
        Manager::getInstance()->hoistManager();
        $this->container = new ContainerBuilder();
    }

    public function startLogger()
    {
        $this->logger = new Logger();
        $this->logger->debug("Booted Hull");
        return $this;
    }

    public function startEventDispatcher()
    {
        $this->eventDispatcher = new EventDispatcher();
        return $this;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function boot()
    {

    }

    public function setServiceLayer($service)
    {
        $this->serviceLayer = new $service();
        return $this;
    }

    public function setHTTPServiceLayer($service, RequestContext $requestContext, $baseUrl)
    {
        $this->serviceLayer = new $service($requestContext, $baseUrl);
        return $this;
    }

    public function __destruct()
    {

    }

    public function getLogger()
    {
        return $this->logger->getLogger();
    }
}