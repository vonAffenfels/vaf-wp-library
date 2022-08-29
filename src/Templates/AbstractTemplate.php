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
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    final public function getDataArray(): array
    {
        return $this->data;
    }

    final public function getData(string $name, $default = null)
    {
        if (!isset($this->data[$name])) {
            return $default;
        }

        return $this->data[$name];
    }

    final public function __get(string $name)
    {
        return $this->getData($name);
    }

    abstract public function render(): string;

    abstract public static function getTemplateExtension(): string;

    abstract public static function registerFunction(string $name, Closure $function): void;
}
