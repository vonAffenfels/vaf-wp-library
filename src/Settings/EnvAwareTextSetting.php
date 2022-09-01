<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Template;

abstract class EnvAwareTextSetting extends TextSetting
{
    /**
     * @var bool
     */
    private bool $fromEnv = false;

    abstract protected function getEnvKey(): string;
    abstract protected function parseEnvValue($value);

    final public function isFromEnv(): bool
    {
        return $this->fromEnv;
    }

    protected function deserialize($value)
    {
        if (!empty($this->getEnvKey())) {
            $env = $this->getEnvValue();
            if (!is_null($env)) {
                $value = $env;
                $this->fromEnv = true;
            }
        }

        return $value;
    }

    private function getEnvValue()
    {
        $env = getenv($this->getEnvKey());
        if (empty($env)) {
            return null;
        }

        return $this->parseEnvValue($env);
    }

    /**
     * @return string
     */
    public function renderInput(): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Fields/Text', [
            'slug' => $this->getSlug(),
            'value' => $this->getValue(),
            'readonly' => $this->isFromEnv()
        ]);
    }
}
