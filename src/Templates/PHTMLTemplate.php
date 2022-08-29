<?php

namespace VAF\WP\Library\Templates;

use Closure;
use VAF\WP\Library\Exceptions\Template\FunctionAlreadyRegistered;
use VAF\WP\Library\Exceptions\Template\FunctionNotRegistered;

final class PHTMLTemplate extends AbstractTemplate
{
    private static array $customFunctions = [];

    public function render(): string
    {
        ob_start();
        include($this->getTemplateFile());
        return ob_get_clean();
    }

    public static function getTemplateExtension(): string
    {
        return 'phtml';
    }

    /**
     * @param string $name
     * @param Closure $function
     * @return void
     * @throws FunctionAlreadyRegistered
     */
    public static function registerFunction(string $name, Closure $function): void
    {
        if (isset(self::$customFunctions[$name])) {
            throw new FunctionAlreadyRegistered($name);
        }

        self::$customFunctions[$name] = $function;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws FunctionNotRegistered
     */
    public function __call(string $name, array $arguments)
    {
        if (!isset(self::$customFunctions[$name])) {
            throw new FunctionNotRegistered($name);
        }

        return call_user_func_array(self::$customFunctions[$name], $arguments);
    }
}
