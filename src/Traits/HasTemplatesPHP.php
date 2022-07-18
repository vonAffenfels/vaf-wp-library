<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Traits;

use Exception;
use VAF\WP\Library\Traits\Internal\HasTemplates;

trait HasTemplatesPHP
{
    use HasTemplates;

    private array $templatePaths = [];

    final protected function startTemplatesPHP(): void
    {
        $this->templatePaths = array_merge($this->getTemplateDirectories(), [
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
     * Should return an array with all paths where to look for templates
     * (in reversed priority order - most important directory as last)
     *
     * @return array
     */
    abstract protected function getTemplateDirectories(): array;

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
}
