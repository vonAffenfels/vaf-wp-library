<?php

namespace VAF\WP\Library\AdminPages;

abstract class AbstractAdminPage
{
    private string $pageTitle;
    private string $menuTitle;
    private string $slug;
    private string $capability;
    private string $icon;
    private ?int $position;

    /**
     * @param string $pageTitle
     * @param string $menuTitle
     * @param string $capability
     * @param string|null $slug
     * @param string $icon
     * @param int|null $position
     * @return void
     */
    final public function __constructor(
        string $pageTitle,
        string $menuTitle,
        string $capability,
        ?string $slug = null,
        string $icon = '',
        ?int $position = null
    ) {
        $this->pageTitle = $pageTitle;
        $this->menuTitle = $menuTitle;
        $this->slug = $slug ?? sanitize_key($pageTitle);
        $this->icon = $icon;
        $this->position = $position;
        $this->capability = $capability;
    }

    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getCapability(): string
    {
        return $this->capability;
    }

    abstract public function render(): string;
}
