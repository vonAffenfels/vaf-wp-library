<?php

namespace VAF\WP\Library\Exceptions\Validator;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Validators\AbstractValidator;

class InvalidValidatorClass extends LogicException
{
    public function __construct(Plugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module Setting] Validator %s needs to extend class %s!',
            $plugin->getPluginSlug(),
            $class,
            AbstractValidator::class
        );
        parent::__construct($message, 0, $previous);
    }
}
