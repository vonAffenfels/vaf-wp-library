<?php

namespace VAF\WP\Library\Exceptions\Template;

use Exception;
use Throwable;

final class NamespaceNotRegistered extends Exception
{
    final public function __construct(string $namespace, Throwable $previous = null)
    {
        $message = sprintf('Template namespace "%s" is not registered!', $namespace);
        parent::__construct($message, 0, $previous);
    }
}