<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Application\Http\Bootstrap;

use Ixocreate\Application\Http\Middleware\MiddlewareConfigurator;
use Ixocreate\Application\Service\Bootstrap\BootstrapItemInterface;
use Ixocreate\Application\Service\Configurator\ConfiguratorInterface;

final class MiddlewareBootstrapItem implements BootstrapItemInterface
{
    /**
     * @return ConfiguratorInterface
     */
    public function getConfigurator(): ConfiguratorInterface
    {
        return new MiddlewareConfigurator();
    }

    /**
     * @return string
     */
    public function getVariableName(): string
    {
        return 'middleware';
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return 'middleware.php';
    }
}
