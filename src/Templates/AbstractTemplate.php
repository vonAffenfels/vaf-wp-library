<?php

namespace VAF\WP\Library\Templates;

use Closure;
use VAF\WP\Library\Exceptions\Template\TemplateFileNotFound;

abstract class AbstractTemplate
{
    private string $templateFile;

    private array $data = [];

    /**
     * @param string $templateFile
     * @throws TemplateFileNotFound
     */
    final public function __construct(string $templateFile)
    {
        if (!file_exists($templateFile) || !is_readable($templateFile)) {
            throw new TemplateFileNotFound($templateFile);
        }

        $this->templateFile = $templateFile;
    }

    final protected function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    final public function setDataArray(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    final public function getDataArray(): array
    {
        return $this->data;
    }

    abstract public function render(): string;

    abstract public static function getTemplateExtension(): string;

    abstract public static function registerFunction(string $name, Closure $function): void;
}
