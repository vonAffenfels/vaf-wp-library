<?php

namespace VAF\WP\Library\Features;

abstract class Templates extends AbstractFeature
{
    abstract public function renderTemplate(string $template, array $context = []): string;
}