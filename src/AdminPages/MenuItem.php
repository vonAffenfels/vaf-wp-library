<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\IsImmutable;

/**
 * Class to represent a menu entry inside the Wordpress backend
 */
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

    /**
     * Constructor
     *
     * @param string $slug
     * @param string $menuTitle
     */
    final public function __construct(string $slug, string $menuTitle)
    {
        $this->slug = $slug;
        $this->menuTitle = $menuTitle;
    }

    /**
     * Return string representation of this object
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("[MenuItem %s - %s]", $this->slug, $this->menuTitle);
    }

    /**
     * Returns the slug for this menu item
     *
     * @return string
     */
    final public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Returns the title of the menu item
     *
     * @return string
     */
    final public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    /**
     * Returns the position of the menu item
     * If returned null, menu item will be positioned at the current bottom
     *
     * @return int|null
     */
    final public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Sets the position of the menu item
     *
     * @param int $position
     * @return $this
     */
    final public function setPosition(int $position): self
    {
        $this->checkLock();

        $this->position = $position;
        return $this;
    }

    /**
     * Returns the icon to display for the menu item
     * Can either be a dashicons class, URL or a base64 encoded image
     *
     * @return string
     */
    final public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Sets the icon to display for the menu item
     * Can either be a dashicons class, URL or a base64 encoded image
     *
     * @param string $icon
     * @return $this
     */
    final public function setIcon(string $icon): self
    {
        $this->checkLock();

        $this->icon = $icon;
        return $this;
    }

    /**
     * Returns the parent for the current menu item
     * Parent can either be a slug of an existing menu item or the classname of an AdminPage.
     * If returned null, menu item has no parent and will be a main menu item
     *
     * @return string|null
     */
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

    /**
     * Returns the title of the first entry of the sub-menu
     * The first entry will always be the same as the main menu item
     *
     * @return string
     */
    final public function getSubMenuTitle(): string
    {
        return $this->subMenuTitle ?? $this->menuTitle;
    }

    /**
     * Sets the title of the first entry of the sub-menu
     * The first entry will always be the same as the main menu item
     *
     * @param string $subMenuTitle
     * @return $this
     */
    final public function setSubMenuTitle(string $subMenuTitle): self
    {
        $this->checkLock();

        $this->subMenuTitle = $subMenuTitle;
        return $this;
    }
}
