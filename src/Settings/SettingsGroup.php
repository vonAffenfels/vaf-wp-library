<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Plugin;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsClass;

abstract class SettingsGroup
{
    abstract protected function getSettings(): array;

    abstract public function getTitle(): string;

    abstract public function getDescription(): string;

    abstract public function getSlug(): string;

    private array $settingsData = [];

    private bool $loaded = false;

    private Plugin $plugin;

    final public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;

        foreach ($this->getSettings() as $setting) {
            if (!is_subclass_of($setting, AbstractSetting::class)) {
                throw new InvalidSettingsClass($this->plugin, $setting);
            }

            new $setting($this, $this->plugin);
        }
    }

    final private function getSettingsSlug(): string
    {
        return $this->plugin->getPluginSlug() . '_' . $this->getSlug();
    }

    final private function loadData(): void
    {
        if (!$this->loaded) {
            $this->settingsData = get_option($this->getSettingsSlug(), []);
            $this->loaded = true;
        }
    }

    final public function getSettingsValue(string $slug)
    {
        $this->loadData();
        return $this->settingsData[$slug] ?? null;
    }

    final public function hasSettingsValue(string $slug): bool
    {
        $this->loadData();
        return isset($this->settingsData[$slug]);
    }
}
