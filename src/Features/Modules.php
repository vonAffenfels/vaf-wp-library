<?php

namespace VAF\WP\Library\Features;

use InvalidArgumentException;
use VAF\WP\Library\Module;

final class Modules extends AbstractFeature
{
    /**
     * @param string[] $modules
     * @return $this
     */
    final public function start(array $modules): self
    {
        foreach ($modules as $module) {
            $this->registerModule($module);
        }

        return $this;
    }

    /**
     * @param string $classname
     * @return void
     */
    final private function registerModule(string $classname): void
    {
        if (!is_subclass_of($classname, 'VAF\WP\Library\Module')) {
            throw new InvalidArgumentException('Module must inherit VAF\WP\Library\Module!');
        }

        /** @var Module $module */
        $module = new $classname();
        $module->setPlugin($this->getPlugin());
        $module->register();
    }
}
