<?php

namespace VAF\WP\Library;

use Closure;
use InvalidArgumentException;
use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;
use VAF\WP\Library\Templates\AbstractTemplate;
use VAF\WP\Library\Templates\PHTMLTemplate;

final class Template
{
    /**
     * @var array Registered namespaces for templates
     */
    private static array $namespaces = [];

    /**
     * @var bool Determines if the template engine has been initialized
     */
    private static bool $initialized = false;

    /**
     * @var string[] List of all available engines
     */
    private static array $engines = [];

    /**
     * @return void
     */
    final private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$engines[PHTMLTemplate::getTemplateExtension()] = PHTMLTemplate::class;

        self::$namespaces['VafWpLibrary'] = [
            realpath(trailingslashit(dirname(__FILE__)) . '../templates/')
        ];

        self::$initialized = true;
    }

    /**
     * Registers the namespace for a plugin
     *
     * @param AbstractPlugin $plugin
     * @return void
     */
    final public static function registerPlugin(AbstractPlugin $plugin): void
    {
        self::initialize();

        $pluginNamespace = Helper::camelize($plugin->getPluginSlug());

        // Namespace already registered
        if (isset(self::$namespaces[$pluginNamespace])) {
            return;
        }

        $namespacePaths = [
            $plugin->getPluginDirectory() . 'templates/'
        ];

        $themeSuffixDirectory = 'templates/' . $plugin->getPluginSlug() . '/';
        $baseThemeDirectory = trailingslashit(get_template_directory());
        $childThemeDirectory = trailingslashit(get_stylesheet_directory());

        array_unshift($namespacePaths, $baseThemeDirectory . $themeSuffixDirectory);
        if ($baseThemeDirectory !== $childThemeDirectory) {
            array_unshift($namespacePaths, $childThemeDirectory . $themeSuffixDirectory);
        }

        self::$namespaces[$pluginNamespace] = $namespacePaths;
    }

    final public static function registerFunction(string $name, Closure $function)
    {
        foreach (self::$engines as $engine) {
            call_user_func([$engine, 'registerFunction'], $name, $function);
        }
    }

    /**
     * @param string $template
     * @param array $context
     * @param bool $echo
     * @return string
     * @throws NamespaceNotRegistered
     * @throws InvalidArgumentException
     * @throws TemplateNotFound
     */
    final public static function render(string $template, array $context, bool $echo = true): string
    {
        $templateParts = explode('/', $template);
        if (count($templateParts) < 2) {
            throw new InvalidArgumentException(
                'Template name should contain namespace and template file. Provided: "' . $template . '"'
            );
        }

        // Extract namespace from provided template name
        $namespace = array_shift($templateParts);
        if (!isset(self::$namespaces[$namespace])) {
            throw new NamespaceNotRegistered($namespace);
        }
        $template = implode('/', $templateParts);

        $engine = null;
        $templateFile = null;
        $found = false;

        // Search for a fitting template in the namespace directories
        foreach (self::$namespaces[$namespace] as $namespaceDirectory) {
            foreach (self::$engines as $extension => $engine) {
                $templateFile = trailingslashit($namespaceDirectory) . $template . '.' . $extension;
                if (file_exists($templateFile) && is_readable($templateFile)) {
                    $found = true;
                    break 2;
                }
            }
        }

        if (!$found) {
            throw new TemplateNotFound($template, self::$namespaces[$namespace]);
        }

        /** @var AbstractTemplate $templateObj */
        $templateObj = new $engine($templateFile);
        $templateObj->setDataArray($context);
        $result = $templateObj->render();

        if ($echo) {
            echo $result;
        }

        return $result;
    }
}
