<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library;

use Closure;
use InvalidArgumentException;
use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;
use VAF\WP\Library\Templates\AbstractTemplate;
use VAF\WP\Library\Templates\PHTMLTemplate;

/**
 * Template Engine
 */
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
     * Initializing the complete template system on demand
     * Is needed to register library namespace and PHTML engine
     *
     * @return void
     */
    private static function initialize(): void
    {
        // Do nothing if already initialized
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
     * Registers the namespace for a plugin and adds all necessary directories
     *
     * @param AbstractPlugin $plugin Plugin to register
     * @return void
     */
    final public static function registerPlugin(AbstractPlugin $plugin): void
    {
        // Make sure we are initialized
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

        // Add parent theme template directory to the top of the list
        array_unshift($namespacePaths, $baseThemeDirectory . $themeSuffixDirectory);

        // If we have a child theme then we will add its template directory at the top most position
        if ($baseThemeDirectory !== $childThemeDirectory) {
            array_unshift($namespacePaths, $childThemeDirectory . $themeSuffixDirectory);
        }

        self::$namespaces[$pluginNamespace] = $namespacePaths;
    }

    /**
     * Registers a custom function with all registered namespaces
     * @todo
     *  Maybe build it so that if we register a new engine afterwards all
     *  registered functions get also registered with the new engine
     *
     * @param  string $name Name of the custom function
     * @param  Closure $function The function itself
     * @return void
     */
    final public static function registerFunction(string $name, Closure $function)
    {
        foreach (self::$engines as $engine) {
            call_user_func([$engine, 'registerFunction'], $name, $function);
        }
    }

    /**
     * De-Registers a custom function with all registered namespaces
     *
     * @param  string $name Name of the custom function
     * @return void
     */
    final public static function deregisterFunction(string $name)
    {
        foreach (self::$engines as $engine) {
            call_user_func([$engine, 'deregisterFunction'], $name);
        }
    }

    /**
     * Renders a template
     *
     * @param  string $template The template to render. First part should be the namespace
     * @param  array $context All the data that should be known to the template
     * @return string
     * @throws InvalidArgumentException
     * @throws NamespaceNotRegistered
     * @throws TemplateNotFound
     */
    final public static function render(string $template, array $context = []): string
    {
        // Make sure we are initialized
        self::initialize();

        // Check if we have a namespace and template
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

        // Create template object and let it render
        /** @var AbstractTemplate $templateObj */
        $templateObj = new $engine($templateFile);
        $templateObj->setDataArray($context);
        return $templateObj->render();
    }

    /**
     * Echos a rendered template
     *
     * @param  string $template The template to render. First part should be the namespace
     * @param  array $context All the data that should be known to the template
     * @return void
     */
    final public static function output(string $template, array $context = []): void
    {
        echo self::render($template, $context);
    }
}
