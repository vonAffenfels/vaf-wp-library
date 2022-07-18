<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use Exception;
use VAF\WP\Library\Plugin;

final class TemplatesPHP extends AbstractFeature
{
    public const FEATURE_NAME = 'templates';

    private array $templatePaths = [];

    final public function __construct(Plugin $plugin, array $templateDirectories)
    {
        $this->setPlugin($plugin);

        $this->templatePaths = array_merge($templateDirectories, [
            trailingslashit(realpath(dirname(__FILE__) . '/../../templatesPhp'))
        ]);
    }

    final private function getTemplateFile(string $template): ?string
    {
        foreach (array_reverse($this->templatePaths) as $templatePath) {
            $templateFile = trailingslashit($templatePath) . $template . '.php';
            if (file_exists($templateFile)) {
                return $templateFile;
            }
        }

        return null;
    }

    /**
     * @throws Exception
     */
    final public function renderTemplate(string $template, array $context = []): string
    {
        $templateFile = $this->getTemplateFile($template);
        if (is_null($templateFile)) {
            throw new Exception('Could not find template "' . $template . '"!');
        }

        extract($context);
        ob_start();
        include($templateFile);
        return ob_get_clean();
    }

    final public function getName(): string
    {
        return self::FEATURE_NAME;
    }
}
