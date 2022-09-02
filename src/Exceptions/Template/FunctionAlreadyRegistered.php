<?php

namespace VAF\WP\Library\Exceptions\Template;

use LogicException;
use Throwable;

final class FunctionAlreadyRegistered extends LogicException
{
    final public function __construct(string $function, Throwable $previous = null)
    {
        $message = sprintf(
            'Function "%s" is already registered with template engines!',
            $function
        );
        parent::__construct($message, 0, $previous);
    }
}
