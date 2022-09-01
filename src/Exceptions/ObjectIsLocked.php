<?php

namespace VAF\WP\Library\Exceptions;

use LogicException;
use Throwable;

final class ObjectIsLocked extends LogicException
{
    final public function __construct(string $object, Throwable $previous = null)
    {
        $message = sprintf("Object %s is locked and can't be modified further!", $object);
        parent::__construct($message, 0, $previous);
    }
}
