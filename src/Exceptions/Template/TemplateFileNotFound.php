<?php

namespace VAF\WP\Library\Exceptions\Template;

use Exception;
use Throwable;

final class TemplateFileNotFound extends Exception
{
    final public function __construct(string $templateFile, Throwable $previous = null)
    {
        $message = sprintf('Could not find or read the template file "%s"!', $templateFile);
        parent::__construct($message, 0, $previous);
    }
}
