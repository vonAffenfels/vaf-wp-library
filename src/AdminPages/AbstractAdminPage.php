<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Plugin;
use VAF\WP\Library\Traits\Internal\HasPlugin;

abstract class AbstractAdminPage
{
    use HasPlugin;

    private string $pageTitle;
    private string $menuTitle;
    private string $slug;
    private string $capability;
    private string $icon;
    private ?int $position;

    /**
     * @param Plugin $plugin
     * @param string $pageTitle
     * @param string $menuTitle
     * @param string $capability
     * @param string|null $slug
     * @param string $icon
     * @param int|null $position
     * @return void
     */
    public function __construct(
        Plugin $plugin,
        string $pageTitle,
        string $menuTitle,
        string $capability,
        ?string $slug = null,
        string $icon = '',
        ?int $position = null
    ) {
        $this->setPlugin($plugin);

        $this->pageTitle = $pageTitle;
        $this->menuTitle = $menuTitle;
        $this->slug = $slug ?? sanitize_key($pageTitle);
        $this->icon = $icon;
        $this->position = $position;
        $this->capability = $capability;
    }

    final public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    final public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    final public function getSlug(): string
    {
        return $this->slug;
    }

    final public function getIcon(): string
    {
        return $this->icon;
    }

    final public function getPosition(): ?int
    {
        return $this->position;
    }

    final public function getCapability(): string
    {
        return $this->capability;
    }

    abstract public function render(): string;
}
