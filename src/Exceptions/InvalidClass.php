<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Exceptions;

use Exception;
use Throwable;
use VAF\WP\Library\Modules\AbstractModule;

final class InvalidClass extends Exception
{
    public function __construct(AbstractModule $module, string $class, string $expected, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module %s] Object of class "%s" does not inherit class "%s"',
            $module->getPlugin()->getPluginName(),
            get_class($module),
            $class,
            $expected
        );
        parent::__construct($message, 0, $previous);
    }
}
