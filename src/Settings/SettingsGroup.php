<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsClass;

abstract class SettingsGroup
{
    abstract public function getSlug(): string;
    abstract public function getTitle(): string;
    abstract public function getDescription(): string;
    abstract public function getSettings(): array;

    private array $values = [];

    private bool $loaded = false;

    private AbstractPlugin $plugin;

    /**
     * @param  AbstractPlugin $plugin
     * @throws InvalidSettingsClass
     */
    final public function __construct(AbstractPlugin $plugin)
    {
        $this->plugin = $plugin;

        foreach ($this->getSettings() as $setting) {
            if (!is_subclass_of($setting, AbstractSetting::class)) {
                throw new InvalidSettingsClass($this->getPlugin(), $setting);
            }

            new $setting($this);
        }
    }

    final public function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    final private function loadValues(): void
    {
        $this->values = get_option(
            $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
            []
        );
        $this->loaded = true;
    }

    final private function isLoaded(): bool
    {
        return $this->loaded;
    }

    final public function getValue(string $slug)
    {
        if (!$this->isLoaded()) {
            $this->loadValues();
        }

        return $this->values[$slug] ?? null;
    }
}
