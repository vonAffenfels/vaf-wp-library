<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library;

abstract class Module
{
    private Plugin $plugin;

    /**
     * Registers hooks for this specific module
     *
     * @return $this
     */
    final public function register(): Module
    {
        foreach ($this->getHooks() as $hook => $callback) {
            if (is_string($callback)) {
                if (in_array($callback, ['__return_false', '__return_true'])) {
                    add_filter($hook, $callback);
                } else {
                    add_filter($hook, [$this, $callback]);
                }
            } elseif (is_array($callback)) {
                if (!isset($callback['callback'])) {
                    throw new \InvalidArgumentException('Missing callback function!');
                }
                $method = $callback['callback'];
                $priority = $callback['priority'] ?? 10;
                $arguments = $callback['arguments'] ?? 1;

                add_filter(
                    $hook,
                    [$this, $method],
                    $priority,
                    $arguments
                );
            }
        }

        return $this;
    }

    /**
     * Function should return all requested hooks
     *
     * ```
     *  return [
     *      'init' => 'callbackFunction',
     *      'activated_plugin' => [
     *          'callback'  => 'callbackFunction',
     *          'priority'  => 10,
     *          'arguments' => 2
     *      ]
     *  ];
     * ```
     *
     * If you want to use `__return_false` or `__return_true`, simple provide them as string
     *
     * @return array
     */
    abstract protected function getHooks(): array;

    /**
     * Setter for $plugin
     *
     * @param Plugin $plugin
     * @return $this
     */
    final public function setPlugin(Plugin $plugin): Module
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Getter for $plugin
     *
     * @return Plugin
     */
    final public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}
