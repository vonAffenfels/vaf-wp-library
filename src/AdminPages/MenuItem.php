<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\IsImmutable;

final class MenuItem
{
    use IsImmutable;

    /**
     * List of predefined menu item slugs
     */
    public const PREDEFINED_MENU_MEDIA = 'upload.php';
    public const PREDEFINED_MENU_COMMENTS = 'edit-comments.php';
    public const PREDEFINED_MENU_POSTS = 'edit.php';
    public const PREDEFINED_MENU_PAGES = 'edit.php?post_type=page';
    public const PREDEFINED_MENU_APPEARANCE = 'themes.php';
    public const PREDEFINED_MENU_PLUGINS = 'plugins.php';
    public const PREDEFINED_MENU_USERS = 'users.php';
    public const PREDEFINED_MENU_TOOLS = 'tools.php';
    public const PREDEFINED_MENU_DASHBOARD = 'index.php';
    public const PREDEFINED_MENU_SETTINGS = 'options-general.php';

    private string $slug;
    private string $menuTitle;
    private ?int $position = null;
    private string $icon = '';
    private ?string $parent = null;
    private ?string $subMenuTitle = null;

    final public function __construct(string $slug, string $menuTitle)
    {
        $this->slug = $slug;
        $this->menuTitle = $menuTitle;
    }

    public function __toString(): string
    {
        return sprintf("[MenuItem %s - %s]", $this->slug, $this->menuTitle);
    }

    final public function getSlug(): string
    {
        return $this->slug;
    }

    final public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    final public function getPosition(): ?int
    {
        return $this->position;
    }

    final public function setPosition(int $position): self
    {
        $this->checkLock();

        $this->position = $position;
        return $this;
    }

    final public function getIcon(): string
    {
        return $this->icon;
    }

    final public function setIcon(string $icon): self
    {
        $this->checkLock();

        $this->icon = $icon;
        return $this;
    }

    final public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * Sets a parent for this menu item.
     * Parent can either be a slug of an existing menu item or the classname of an AdminPage.
     *
     * @param string $parent
     * @return $this
     */
    final public function setParent(string $parent): self
    {
        $this->checkLock();

        $this->parent = $parent;
        return $this;
    }

    final public function getSubMenuTitle(): string
    {
        return $this->subMenuTitle ?? $this->menuTitle;
    }

    final public function setSubMenuTitle(string $subMenuTitle): self
    {
        $this->checkLock();

        $this->subMenuTitle = $subMenuTitle;
        return $this;
    }
}
