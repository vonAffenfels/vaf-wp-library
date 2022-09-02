<?php

namespace VAF\WP\Library\Templates;

use Closure;
use VAF\WP\Library\Exceptions\Template\FunctionAlreadyRegistered;
use VAF\WP\Library\Exceptions\Template\FunctionNotRegistered;

final class PHTMLTemplate extends AbstractTemplate
{
    /**
     * @var array Registered custom functions
     */
    private static array $customFunctions = [];

    /**
     * Renders a .phtml file and returns the output
     *
     * @return string
     */
    final public function render(): string
    {
        ob_start();
        include($this->getTemplateFile());
        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    final public static function getTemplateExtension(): string
    {
        return 'phtml';
    }

    /**
     * @inheritDoc
     */
    final public static function registerFunction(string $name, Closure $function): void
    {
        if (isset(self::$customFunctions[$name])) {
            throw new FunctionAlreadyRegistered($name);
        }

        self::$customFunctions[$name] = $function;
    }

    /**
     * @inheritDoc
     */
    final public static function deregisterFunction(string $name): void
    {
        if (isset(self::$customFunctions[$name])) {
            throw new FunctionNotRegistered($name);
        }

        unset(self::$customFunctions[$name]);
    }

    /**
     * Will get called if an unknown function is called
     * Checks if it is a registered custom function and calls it
     *
     * @param  string $name Name of the function that got called
     * @param  array $arguments List of arguments passed to the function
     * @return mixed
     */
    final public function __call(string $name, array $arguments)
    {
        if (!isset(self::$customFunctions[$name])) {
            throw new FunctionNotRegistered($name);
        }

        return call_user_func_array(self::$customFunctions[$name], $arguments);
    }

    /**
     * Getter for a specific data key
     * If the key is not found will return the default provided
     *
     * @param  string $name Name of the data to get
     * @param  mixed $default Default value if the data is not found
     * @return mixed
     */
    final public function getData(string $name, $default = null)
    {
        $data = $this->getDataArray();

        if (!isset($data[$name])) {
            return $default;
        }

        return $data[$name];
    }

    /**
     * Magic function that gets called if a property is not found inside the class
     * Will call getData() function with a default of null
     *
     * @param  string $name Name of the property that got accessed
     * @return mixed|null
     */
    final public function __get(string $name)
    {
        return $this->getData($name);
    }
}
