<?php
/**
 * kiwi-suite/application (https://github.com/kiwi-suite/application)
 *
 * @package kiwi-suite/application
 * @see https://github.com/kiwi-suite/application
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\Application;

use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;

final class Bootstrap
{
    /**
     * @param string $bootstrapDirectory
     * @param ApplicationInterface $application
     * @return ServiceManager
     */
    public function bootstrap(string $bootstrapDirectory, ApplicationInterface $application): ServiceManager
    {
        $applicationConfig = $this->createApplicationConfig($bootstrapDirectory, $application);
        $serviceRegistry = (new ServiceHandler())->loadFromCache($applicationConfig);
        $serviceRegistry->add(ApplicationConfig::class, $applicationConfig);

        $serviceManager = $this->createServiceManager(
            $serviceRegistry->get(ServiceManagerConfig::class),
            $serviceRegistry
        );

        foreach ($applicationConfig->getPackages() as $package) {
            $package->boot($serviceManager);
        }

        return $serviceManager;
    }

    /**
     * @param string $bootstrapDirectory
     * @param ApplicationInterface $application
     * @return ApplicationConfig
     */
    private function createApplicationConfig(string $bootstrapDirectory, ApplicationInterface $application) : ApplicationConfig
    {
        $applicationConfigurator = new ApplicationConfigurator($bootstrapDirectory);

        $application->configure($applicationConfigurator);

        if (\file_exists($applicationConfigurator->getBootstrapDirectory() . 'application.php')) {
            IncludeHelper::include(
                $applicationConfigurator->getBootstrapDirectory() . 'application.php',
                ['applicationConfigurator' => $applicationConfigurator]
            );
        }

        return $applicationConfigurator->getApplicationConfig();
    }

    /**
     * @param ServiceManagerConfig $serviceManagerConfig
     * @param ServiceRegistry $serviceRegistry
     * @return ServiceManager
     */
    private function createServiceManager(ServiceManagerConfig $serviceManagerConfig, ServiceRegistry $serviceRegistry): ServiceManager
    {
        return new ServiceManager(
            $serviceManagerConfig,
            new ServiceManagerSetup(
                $serviceRegistry->get(ApplicationConfig::class)->getPersistCacheDirectory() . 'servicemanager/',
                !$serviceRegistry->get(ApplicationConfig::class)->isDevelopment()
            ),
            $serviceRegistry->all()
        );
    }
}
