<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Application\Http\BootstrapItem;

use Ixocreate\Application\Http\Pipe\PipeConfigurator;
use Ixocreate\Application\BootstrapItemInterface;
use Ixocreate\Application\ConfiguratorInterface;

final class PipeBootstrapItem implements BootstrapItemInterface
{
    /**
     * @return ConfiguratorInterface
     */
    public function getConfigurator(): ConfiguratorInterface
    {
        return new PipeConfigurator();
    }

    /**
     * @return string
     */
    public function getVariableName(): string
    {
        return 'pipe';
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return 'pipe.php';
    }
}
