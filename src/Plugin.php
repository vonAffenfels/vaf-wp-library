<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library;

use Exception;
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
     * @return $this
     * @throws InvalidArgumentException
     * @throws Exception
     */
    final protected function registerFeature(string $class, array $options): self
    {
        if (isset($this->features[$class])) {
            throw new InvalidArgumentException(sprintf('Feature "%s" already registered!', $class));
        }

        if (!is_subclass_of($class, 'VAF\WP\Library\Features\AbstractFeature')) {
            throw new InvalidArgumentException(sprintf('Feature "%s" has to extend "VAF\WP\Library\Features\AbstractFeature"!', $class));
        }

        $feature = $class::getInstance();
        $feature->setPlugin($this);
        $feature->configure($options);
        $feature->start();

        $this->features[$class] = $feature;

        return $this;
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
