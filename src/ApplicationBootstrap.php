<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Application;

use Ixocreate\Application\Bootstrap\BootstrapFactoryInterface;
use Ixocreate\Application\Bootstrap\BootstrapItemInclude;
use Ixocreate\Application\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerInterface;

final class ApplicationBootstrap
{
    /**
     * @param string $bootstrapDirectory
     * @param string $applicationCacheDirectory
     * @param ApplicationInterface $application
     * @param BootstrapFactoryInterface $bootstrapFactory
     * @return ServiceManagerInterface
     */
    public function bootstrap(
        string $bootstrapDirectory,
        string $applicationCacheDirectory,
        ApplicationInterface $application,
        BootstrapFactoryInterface $bootstrapFactory,
        bool $useApplicationConfigCache = true
    ): ServiceManagerInterface {
        if ($useApplicationConfigCache && \file_exists($applicationCacheDirectory . 'application.cache')) {
            $applicationConfig = @\unserialize(
                \file_get_contents($applicationCacheDirectory . 'application.cache')
            );
        } else {
            $applicationConfig = $this->save($bootstrapDirectory, $applicationCacheDirectory, $application, $bootstrapFactory);
        }

        $serviceRegistry = $bootstrapFactory->createServiceHandler()->load($applicationConfig);
        $serviceRegistry->add(ApplicationConfig::class, $applicationConfig);

        $serviceManager = $bootstrapFactory->createServiceManager(
            $serviceRegistry->get(ServiceManagerConfig::class),
            $applicationConfig,
            $serviceRegistry
        );

        foreach ($applicationConfig->getBootPackages() as $package) {
            $package->boot($serviceManager);
        }

        return $serviceManager;
    }

    public function save(
        string $bootstrapDirectory,
        string $applicationCacheDirectory,
        ApplicationInterface $application,
        BootstrapFactoryInterface $bootstrapFactory,
        bool $force = false
    ): ApplicationConfig {
        $applicationConfig = $this->createApplicationConfig(
            $bootstrapFactory->createApplicationConfigurator($bootstrapDirectory),
            $application
        );

        if (!$applicationConfig->isDevelopment() || $force) {
            if (!\file_exists($applicationCacheDirectory)) {
                \mkdir($applicationCacheDirectory, 0777, true);
            }
            \file_put_contents($applicationCacheDirectory . 'application.cache', \serialize($applicationConfig));
        }

        return $applicationConfig;
    }

    /**
     * @param ApplicationConfigurator $applicationConfigurator
     * @param ApplicationInterface $application
     * @return ApplicationConfig
     */
    private function createApplicationConfig(
        ApplicationConfigurator $applicationConfigurator,
        ApplicationInterface $application
    ): ApplicationConfig {
        $include = function ($bootstrapFile) use ($applicationConfigurator) {
            if (\file_exists($bootstrapFile)) {
                BootstrapItemInclude::include(
                    $bootstrapFile,
                    ['application' => $applicationConfigurator]
                );
            }
        };

        $include($applicationConfigurator->getBootstrapDirectory() . 'application.php');
        $include($applicationConfigurator->getBootstrapDirectory() . $applicationConfigurator->getBootstrapEnvDirectory() . 'application.php');

        $application->configure($applicationConfigurator);

        return $applicationConfigurator->getApplicationConfig();
    }
}
