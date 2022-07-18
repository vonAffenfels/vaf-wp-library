<?php

namespace VAF\WP\Library\Traits\Internal;

trait HasTemplates
{
    abstract public function renderTemplate(string $template, array $context = []): string;
}
