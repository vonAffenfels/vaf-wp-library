<?php

namespace VAF\WP\Library\Exceptions;

use Exception;
use Throwable;

class FeatureAlreadyStartedException extends Exception
{
    /**
     * @param string $feature
     * @param Throwable|null $previous
     */
    public function __construct(string $feature, ?Throwable $previous = null)
    {
        $message = sprintf('Feature "%s" is already started!', $feature);
        parent::__construct($message, 0, $previous);
    }
}