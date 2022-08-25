<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Modules;

use VAF\WP\Library\PluginAPI\AbstractPluginAPI;

final class PluginAPIModule extends AbstractHookModule
{
    /**
     * Returns a callable that is run to configure the module
     *
     * @param  string $apiClass
     * @return callable
     */
    final public static function configure(string $apiClass): callable
    {
        return function (PluginAPIModule $module) use ($apiClass) {
            $module->apiClass = $apiClass;
        };
    }

    /**
     * @var string Class of plugin API
     */
    private string $apiClass = AbstractPluginAPI::class;

    /**
     * @var AbstractPluginAPI|null Instance of plugin API
     */
    private ?AbstractPluginAPI $instance = null;

    /**
     * Returns instance of plugin API
     *
     * @return AbstractPluginAPI
     */
    public function getPluginAPI(): AbstractPluginAPI
    {
        if (is_null($this->instance)) {
            $this->instance = new $this->apiClass();
        }

        return $this->instance;
    }

    /**
     * @return array
     */
    protected function getHooks(): array
    {
        return [
            'vaf-get-plugin' => [
                self::ARGUMENTS => 2,
                self::CALLBACK => function (?AbstractPluginAPI $return, string $plugin) {
                    if ($plugin === $this->getPlugin()->getPluginSlug()) {
                        $return = $this->getPluginAPI();
                    }

                    return $return;
                }
            ]
        ];
    }
}
