<?php

namespace VAF\WP\Library\Features;

use Exception;
use InvalidArgumentException;
use VAF\WP\Library\Plugin;

abstract class AbstractFeature
{
    private Plugin $plugin;

    private static array $instances = [];

    private array $parameters = [];

    final private function __construct()
    {
    }

    final public static function getInstance(): self
    {
        if (is_null(static::$instances[static::class] ?? null)) {
            static::$instances[static::class] = new static();
        }

        return static::$instances[static::class];
    }

    final public function setPlugin(Plugin $plugin): self
    {
        $this->plugin = $plugin;

        return $this;
    }

    final public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    final public function configure(array $parameters): self
    {
        foreach ($this->getParameters() as $name => $config) {
            if (isset($parameters[$name])) {
                $this->parameters[$name] = $parameters[$name];
            } else {
                if ($config['required'] ?? false) {
                    throw new InvalidArgumentException(sprintf('Missing parameter "%s" for feature %s', $name, get_class($this)));
                }

                if (isset($config['default'])) {
                    $this->parameters[$name] = $config['default'];
                }
            }
        }

        return $this;
    }

    final protected function getParameter(string $name)
    {
        if (!isset($this->parameters[$name])) {
            throw new InvalidArgumentException(sprintf('Parameter "%s" was not configured!', $name));
        }

        return $this->parameters[$name];
    }

    abstract public function start(): self;

    abstract protected function getParameters(): array;
}
