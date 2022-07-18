<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Traits;

use VAF\WP\Library\Settings\Section;
use VAF\WP\Library\Settings\Tab;
use VAF\WP\Library\Traits\Internal\HasTemplates;

trait HasSettings
{
    use HasTemplates;

    final protected function startSettings(): void
    {
        $namespace = $this->getSettingNamespace();

        /** @var Tab $tab */
        foreach ($this->getSettings() as $tab) {
            $settingsSlug = $namespace . '-' . $tab->getSlug();

            register_setting(
                $settingsSlug,
                $settingsSlug
            );

            /** @var Section $section */
            foreach ($tab->getSections() as $section) {
                add_settings_section(
                    $section->getSlug(),
                    $section->getTitle(),
                    function () use ($section) {
                        echo $this->renderTemplate('Settings/Section/Header', [
                            'description' => $section->getDescription()
                        ]);
                    },
                    $settingsSlug
                );
            }
        }
    }

    abstract protected function getSettings(): array;

    abstract protected function getSettingNamespace(): string;
}
