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
            }
        }

        return $value;
    }

    protected function serialize($value)
    {
        if (!$this->isLoaded()) {
            // We need to check if we have an env value
            $this->getEnvValue();
        }

        if ($this->isFromEnv()) {
            // Do not save to database if from env
            return null;
        }

        return $value;
    }

    private function getEnvValue()
    {
        $env = getenv($this->getEnvKey());
        if (empty($env)) {
            return null;
        }

        $env = $this->parseEnvValue($env);
        if (!is_null($env)) {
            $this->fromEnv = true;
        }

        return $env;
    }

    /**
     * @param null $displayValue
     * @return string
     */
    public function renderInput($displayValue = null): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Fields/Text', [
            'slug' => $this->getSlug(),
            'value' => $displayValue ?? $this->getValue(),
            'readonly' => $this->isFromEnv()
        ]);
    }

    protected function getDefault(): string
    {
        $value = parent::getDefault();

        // Default to the env content
        if (!empty($this->getEnvKey())) {
            $env = $this->getEnvValue();
            if (!is_null($env)) {
                $value = $env;
            }
        }

        return $value;
    }
}
