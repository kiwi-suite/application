<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Application\Bootstrap;

use Ixocreate\Application\Configurator\ConfiguratorInterface;

interface BootstrapItemInterface
{
    /**
     * @return mixed
     */
    public function getConfigurator(): ConfiguratorInterface;

    /**
     * @return string
     */
    public function getVariableName(): string;

    /**
     * @return string
     */
    public function getFileName(): string;
}
