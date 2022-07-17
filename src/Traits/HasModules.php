<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Traits;

use InvalidArgumentException;
use VAF\WP\Library\Module;
use VAF\WP\Library\Plugin;

trait HasModules
{
    /**
     * List of registered modules
     *
     * @var Module[]
     */
    private array $modules = [];

    final protected function startModules(): void
    {
        foreach ($this->getModules() as $module) {
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

        /** @var Plugin $this */
        $module->setPlugin($this);
        $module->register();
    }

    abstract protected function getModules(): array;
}
