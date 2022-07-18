<?php

namespace VAF\WP\Library\Settings;

class Tab
{
    private array $sections = [];
    private string $slug;
    private string $title;

    final public function __constructor(string $slug, string $title)
    {
        $this->slug = $slug;
        $this->title = $title;
    }

    final public function registerSection(Section $section): self
    {
        $this->sections[] = $section;

        return $this;
    }

    final public function getSections(): array
    {
        return $this->sections;
    }

    final public function getSlug(): string
    {
        return $this->slug;
    }

    final public function getTitle(): string
    {
        return $this->title;
    }
}
