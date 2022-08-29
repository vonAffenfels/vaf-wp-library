<?php

namespace VAF\WP\Library\Exceptions\Template;

use Exception;
use Throwable;
use VAF\WP\Library\Helper;

final class TemplateNotFound extends Exception
{
    final public function __construct(string $template, array $searchPath, Throwable $previous = null)
    {
        $message = sprintf(
            'Could not find the template "%s"! Searched in directories %s',
            $template,
            Helper::implodeWithLast($searchPath, ', ', ' and ')
        );
        parent::__construct($message, 0, $previous);
    }
}
