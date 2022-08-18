<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Settings;

class EnvAwareTextSetting extends TextSetting
{
    /**
     * @var callable|null
     */
    private $envParser = null;

    /**
     * @var string
     */
    private string $envKey = '';

    /**
     * @var bool
     */
    private bool $fromEnv = false;

    final public function setEnvKey(string $envKey): self
    {
        $this->envKey = $envKey;
        return $this;
    }

    final public function setEnvParser(callable $envParser): self
    {
        $this->envParser = $envParser;
        return $this;
    }

    final public function isFromEnv(): bool
    {
        return $this->fromEnv;
    }

    protected function parseValue($value)
    {
        if (!empty($this->envKey)) {
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
        $env = getenv($this->envKey);
        if (empty($env)) {
            return null;
        }

        if (is_callable($this->envParser)) {
            $parser = $this->envParser;
            $env = $parser($env);
        }

        return $env;
    }
}
