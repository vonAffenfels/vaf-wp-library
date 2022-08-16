<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Modules;

use VAF\WP\Library\Exceptions\Module\Hook\HookInvalidConfiguration;
use VAF\WP\Library\Exceptions\Module\Hook\HookMissingCallback;

abstract class AbstractHookModule extends AbstractModule
{
    protected const CALLBACK = 'callback';
    protected const PRIORITY = 'priority';
    protected const ARGUMENTS = 'arguments';

    //<editor-fold desc="Initialisation functions">
    /****************************
     * Initialisation functions *
     ****************************/

    /**
     * Starts the hook module
     *
     * @throws HookMissingCallback
     * @throws HookInvalidConfiguration
     */
    final public function start(): void
    {
        foreach ($this->getHooks() as $hook => $callback) {
            if (is_string($callback)) {
                if (in_array($callback, ['__return_false', '__return_true'])) {
                    add_filter($hook, $callback);
                } else {
                    add_filter($hook, [$this, $callback]);
                }
            } elseif (is_array($callback)) {
                if (!isset($callback[self::CALLBACK])) {
                    throw new HookMissingCallback($this->getPlugin(), $this, $hook);
                }
                $method = $callback[self::CALLBACK];
                $priority = $callback[self::PRIORITY] ?? 10;
                $arguments = $callback[self::ARGUMENTS] ?? 1;

                if (!is_callable($method)) {
                    $method = [$this, $method];
                }

                add_filter(
                    $hook,
                    $method,
                    $priority,
                    $arguments
                );
            } elseif (is_callable($callback)) {
                add_filter($hook, $callback);
            } else {
                throw new HookInvalidConfiguration($this->getPlugin(), $this, $hook);
            }
        }
    }
    //</editor-fold>

    //<editor-fold desc="Abstract function definitions">
    /*********************************
     * Abstract function definitions *
     *********************************/

    /**
     * Function should return all requested hooks
     *
     * ```
     *  return [
     *      'init' => 'callbackFunction',
     *      'activated_plugin' => [
     *          self::CALLBACK  => 'callbackFunction',
     *          self::PRIORITY  => 10,
     *          self::ARGUMENTS => 2
     *      ]
     *  ];
     * ```
     *
     * If you want to use `__return_false` or `__return_true`, simple provide them as string
     *
     * @return array
     */
    abstract protected function getHooks(): array;
    //</editor-fold>
}
