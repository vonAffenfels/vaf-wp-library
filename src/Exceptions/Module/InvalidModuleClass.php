<?php

namespace VAF\WP\Library\Exceptions\Module;

use LogicException;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Modules\AbstractModule;

final class InvalidModuleClass extends LogicException
{
    public function __construct(AbstractPlugin $plugin, string $moduleClass, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Module %s needs to extend %s!',
            $plugin->getPluginSlug(),
            $moduleClass,
            AbstractModule::class
        );
        parent::__construct($message, 0, $previous);
    }
}
