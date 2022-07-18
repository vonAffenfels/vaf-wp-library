<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use InvalidArgumentException;
use VAF\WP\Library\Module;

final class Modules extends AbstractFeature
{
    final public function start(): self
    {
        /** @var string $module */
        foreach ($this->getParameter('modules') as $module) {
            $this->registerModule($module);
        }

        return $this;
    }

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

    final protected function getParameters(): array
    {
        return [
            'modules' => [
                'required' => true
            ]
        ];
    }
}
