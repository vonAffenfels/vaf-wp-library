<?php

namespace VAF\WP\Library\Exceptions\Template;

use Exception;
use Throwable;
use VAF\WP\Library\Helper;

final class FunctionAlreadyRegistered extends Exception
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
