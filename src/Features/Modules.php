<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use InvalidArgumentException;
use VAF\WP\Library\Module;
use VAF\WP\Library\Plugin;

final class Modules extends AbstractFeature
{
    public const FEATURE_NAME = 'modules';

    /**
     * List of registered modules
     *
     * @var Module[]
     */
    private array $modules = [];

    final public function __construct(Plugin $plugin, array $modules)
    {
        $this->setPlugin($plugin);

        foreach ($modules as $module) {
            $this->registerModule($module);
        }
    }

    final private function registerModule(string $classname): void
    {
        // If we already have the module class registered
        // we don't want to do it again
        if (isset($this->modules[$classname])) {
            return;
        }

        if (!is_subclass_of($classname, 'VAF\WP\Library\Module')) {
            throw new InvalidArgumentException('Module must inherit VAF\WP\Library\Module!');
        }

        /** @var Module $module */
        $module = new $classname();
        $module->setPlugin($this->getPlugin());
        $module->register();
    }

    final public function getName(): string
    {
        return self::FEATURE_NAME;
    }
}
