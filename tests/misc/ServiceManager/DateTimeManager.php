<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\Application\ServiceManager;

use Ixocreate\ServiceManager\SubManager\AbstractSubManager;

class DateTimeManager extends AbstractSubManager
{
    const INSTANCE_OF = \DateTimeInterface::class;
}
