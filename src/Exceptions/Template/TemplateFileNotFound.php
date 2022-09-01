<?php

namespace VAF\WP\Library\Exceptions\Template;

use LogicException;
use Throwable;

final class TemplateFileNotFound extends LogicException
{
    final public function __construct(string $templateFile, Throwable $previous = null)
    {
        $message = sprintf('Could not find or read the template file "%s"!', $templateFile);
        parent::__construct($message, 0, $previous);
    }
}
