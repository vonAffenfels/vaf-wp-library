<?php

namespace VAF\WP\Library\Exceptions\Template;

use LogicException;
use Throwable;

final class EngineAlreadyRegistered extends LogicException
{
    final public function __construct(string $extension, Throwable $previous = null)
    {
        $message = sprintf('Template engine for extension "%s" is already registered!', $extension);
        parent::__construct($message, 0, $previous);
    }
}