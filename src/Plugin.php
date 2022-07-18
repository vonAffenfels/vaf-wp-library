<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library;

use VAF\WP\Library\AdminPages\AbstractAdminPage;
use VAF\WP\Library\Exceptions\FeatureAlreadyStartedException;
use VAF\WP\Library\Features\AdminPages;
use VAF\WP\Library\Features\Modules;
use VAF\WP\Library\Features\AbstractFeature;
use VAF\WP\Library\Features\PluginCommunication;
use VAF\WP\Library\Features\RestAPI;
use VAF\WP\Library\Features\Shortcodes;

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

    /**
     * Starts the plugin and creates a new instance
     *
     * @param string $pluginFile
     * @return void
     */
    final public static function start(string $pluginFile): void
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($pluginFile);
        }
    }

    /**
     * @param string $pluginFile
     */
    final private function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;

        $this->initPlugin();
    }

    /**
     * @param string[] $modules
     * @return $this
     * @throws FeatureAlreadyStartedException
     */
    final protected function startModules(array $modules): self
    {
        if (isset($this->features[Modules::class])) {
            throw new FeatureAlreadyStartedException('Modules');
        }

        $instance = Modules::getInstance();
        $instance->setPlugin($this);
        $instance->start($modules);

        $this->features[Modules::class] = $instance;

        return $this;
    }

    /**
     * @param AbstractAdminPage $adminPage
     * @return $this
     * @throws FeatureAlreadyStartedException
     */
    final protected function startAdminPages(AbstractAdminPage $adminPage): self
    {
        if (isset($this->features[AdminPages::class])) {
            throw new FeatureAlreadyStartedException('AdminPages');
        }

        $instance = AdminPages::getInstance();
        $instance->setPlugin($this);
        $instance->start($adminPage);

        $this->features[AdminPages::class] = $instance;

        return $this;
    }

    /**
     * @param mixed $obj
     * @return $this
     * @throws FeatureAlreadyStartedException
     */
    final protected function startPluginCommunication($obj = null): self
    {
        if (isset($this->features[PluginCommunication::class])) {
            throw new FeatureAlreadyStartedException('PluginCommunication');
        }

        $instance = PluginCommunication::getInstance();
        $instance->setPlugin($this);
        $instance->start($obj);

        $this->features[PluginCommunication::class] = $instance;

        return $this;
    }

    /**
     * @param string $restNamespace
     * @param string[] $restRoutes
     * @return $this
     * @throws FeatureAlreadyStartedException
     */
    final protected function startRestApi(string $restNamespace, array $restRoutes): self
    {
        if (isset($this->features[RestAPI::class])) {
            throw new FeatureAlreadyStartedException('RestAPI');
        }

        $instance = RestAPI::getInstance();
        $instance->setPlugin($this);
        $instance->start($restNamespace, $restRoutes);

        $this->features[RestAPI::class] = $instance;

        return $this;
    }

    /**
     * @param string[] $shortcodes
     * @return $this
     * @throws FeatureAlreadyStartedException
     */
    final protected function startShortcodes(array $shortcodes): self
    {
        if (isset($this->features[Shortcodes::class])) {
            throw new FeatureAlreadyStartedException('Shortcodes');
        }

        $instance = Shortcodes::getInstance();
        $instance->setPlugin($this);
        $instance->start($shortcodes);

        $this->features[Shortcodes::class] = $instance;

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
     * @return string
     */
    public function getPluginName(): string
    {
        return basename($this->getPluginFile(), '.php');
    }

    /**
     * Function is called when the plugin starts and is initialized
     */
    abstract protected function initPlugin(): void;
}
