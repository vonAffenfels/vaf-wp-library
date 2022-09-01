<?php

namespace VAF\WP\Library\Settings;

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

    protected function parseValue($value)
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
}
