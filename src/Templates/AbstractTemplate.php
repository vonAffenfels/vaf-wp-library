<?php

namespace VAF\WP\Library\Templates;

use Closure;
use VAF\WP\Library\Exceptions\Template\TemplateFileNotFound;

abstract class AbstractTemplate
{
    /**
     * @var string Template file for this template object
     */
    private string $templateFile;

    /**
     * @var array Data that is known to the template
     */
    private array $data = [];

    /**
     * Creates a new template object. Checks if the provided file exists and is readable
     *
     * @param  string $templateFile
     */
    final public function __construct(string $templateFile)
    {
        if (!file_exists($templateFile) || !is_readable($templateFile)) {
            throw new TemplateFileNotFound($templateFile);
        }

        $this->templateFile = $templateFile;
    }

    /**
     * Getter for the template file
     *
     * @return string
     */
    final protected function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    /**
     * Setter for the template data
     *
     * @param  array $data
     * @return $this
     */
    final public function setDataArray(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Getter for the template data
     *
     * @return array
     */
    final public function getDataArray(): array
    {
        return $this->data;
    }

    /**
     * Abstract function that renders the template and returns the rendered content
     *
     * @return string
     */
    abstract public function render(): string;

    /**
     * Abstract function that should return the file extension (without a dot) that this engine will handle
     *
     * @return string
     */
    abstract public static function getTemplateExtension(): string;

    /**
     * Abstract function to register a custom function with this engine
     *
     * @param  string $name Name of the function
     * @param  Closure $function The function itself
     * @return void
     */
    abstract public static function registerFunction(string $name, Closure $function): void;

    /**
     * Abstract function to deregister a custom function with this engine
     *
     * @param  string $name Name of the function
     * @return void
     */
    abstract public static function deregisterFunction(string $name): void;
}
