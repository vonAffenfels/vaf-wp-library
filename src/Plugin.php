<?php

namespace VAF\WP\Library;

abstract class Plugin
{
    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    final protected function __contruct()
    {
        $this->initPlugin();

        $this->startTrait('modules');
        $this->startTrait('shortcodes');
    }

    final private function startTrait(string $trait): void
    {
        $startMethod = 'start' . ucfirst($trait);
        if (method_exists($this, $startMethod)) {
            $this->$startMethod();
        }
    }

    /**
     * Starts the plugin and creates a new instance
     *
     * @return void
     */
    public static function start(): void
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
    }

    /**
     * Function is called when the plugin starts and is initialized
     */
    abstract protected function initPlugin(): void;
}
