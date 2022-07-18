<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library;

use InvalidArgumentException;
use VAF\WP\Library\Features\AbstractFeature;

abstract class Plugin
{
    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * @var AbstractFeature[]
     */
    private array $features = [];

    /**
     * Path to the main plugin file
     *
     * @var string
     */
    private string $pluginFile;

    final private function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;

        $this->initPlugin();
    }

    /**
     * Registers a specific feature to this plugin instance
     *
     * @param AbstractFeature $feature
     * @return $this
     * @throws InvalidArgumentException
     */
    final protected function registerFeature(AbstractFeature $feature): self
    {
        $name = $feature->getName();

        if (isset($this->features[$name])) {
            throw new InvalidArgumentException(sprintf('Feature "%s" already registered!', $name));
        }
        $this->features[$name] = $feature;

        return $this;
    }

    /**
     * Returns a registered feature or null if feature is not found
     *
     * @param string $name
     * @return AbstractFeature|null
     */
    final public function getFeature(string $name): ?AbstractFeature
    {
        return $this->features[$name] ?? null;
    }

    /**
     * @return string
     */
    final public function getPluginFile(): string
    {
        return $this->pluginFile;
    }

    /**
     * @return string
     */
    final public function getPluginDirectory(): string
    {
        return plugin_dir_path($this->getPluginFile());
    }

    /**
     * Starts the plugin and creates a new instance
     *
     * @param string $pluginFile
     * @return void
     */
    public static function start(string $pluginFile): void
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($pluginFile);
        }
    }

    /**
     * Function is called when the plugin starts and is initialized
     */
    abstract protected function initPlugin(): void;

    public function getPluginName(): string
    {
        return basename($this->getPluginFile(), '.php');
    }
}
