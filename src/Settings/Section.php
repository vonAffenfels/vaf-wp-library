<?php

namespace VAF\WP\Library\Settings;

class Section
{
    private string $slug;
    private string $title;
    private string $description;

    private array $settings = [];

    final public function __constructor(string $slug, string $title, string $description = '')
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->description = $description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function registerSetting(AbstractSetting $setting): self
    {
        $this->settings[] = $setting;

        return $this;
    }
}
