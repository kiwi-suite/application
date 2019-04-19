<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Application\Service;

use Ixocreate\ServiceManager\Factory\AutowireFactory;

final class SubManagerConfigurator extends AbstractServiceManagerConfigurator
{
    /**
     * @var array
     */
    private $metadata = [];

    /**
     * ServiceManagerConfigurator constructor.
     *
     * @param string $subManagerName
     * @param string $validation
     * @param string $defaultAutowireFactory
     */
    public function __construct(
        string $subManagerName,
        string $validation,
        string $defaultAutowireFactory = AutowireFactory::class
    ) {
        parent::__construct($defaultAutowireFactory);
        $this->metadata['validation'] = $validation;
        $this->metadata['subManagerName'] = $subManagerName;
    }

    /**
     * @param string $directory
     * @param bool $recursive
     * @param array $only
     */
    public function addDirectory(string $directory, bool $recursive = true, array $only = []): void
    {
        $only[] = $this->metadata['validation'];
        parent::addDirectory($directory, $recursive, \array_unique($only));
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function getSubManagers(): array
    {
        return [];
    }

    /**
     * @param ServiceRegistryInterface $serviceRegistry
     * @return void
     */
    public function registerService(ServiceRegistryInterface $serviceRegistry): void
    {
        $serviceRegistry->add($this->metadata['subManagerName'] . '::Config', $this->getServiceManagerConfig());
    }
}
