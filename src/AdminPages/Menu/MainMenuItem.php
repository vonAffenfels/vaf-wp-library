<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages\Menu;

use Closure;
use VAF\WP\Library\Exceptions\ObjectIsLocked;

final class MainMenuItem extends AbstractMenuItem
{
    /**
     * @var ChildMenuItem[] Child menu items
     */
    private array $children = [];

    /**
     * Returns if this menu item has children
     *
     * @return bool
     */
    final public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Returns a list of ChildMenuItem
     *
     * @return ChildMenuItem[]
     */
    final public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Adds a new child to this menu item if the object is not already locked
     *
     * @param  ChildMenuItem $child New child to add
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function addChild(ChildMenuItem $child): self
    {
        $this->checkLock();

        $this->children[] = $child;
        return $this;
    }

    /**
     * @var string Title for the first item in the submenu
     */
    private string $subMenuTitle;

    /**
     * @var string Icon to display in the top level menu
     */
    private string $icon;

    /**
     * Creates a new menu item
     *
     * @param string $key Key of the new menu item (will be part of the slug)
     * @param string $menuTitle Title inside the menu
     * @param string $rendererClass Classname for the page renderer
     * @param Closure|null $configureFunc Function to configure the page renderer
     */
    final public function __construct(
        string $key,
        string $menuTitle,
        string $rendererClass = '',
        ?Closure $configureFunc = null
    ) {
        parent::__construct($key, $menuTitle, $rendererClass, $configureFunc);

        $this->subMenuTitle = $menuTitle;
        $this->icon = '';
    }

    /**
     * Returns the title for the first submenu item
     *
     * @return string
     */
    final public function getSubMenuTitle(): string
    {
        return $this->subMenuTitle;
    }

    /**
     * Sets the title for the first submenu item if object is not locked
     *
     * @param  string $subMenuTitle The new title
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function setSubMenuTitle(string $subMenuTitle): self
    {
        $this->checkLock();

        $this->subMenuTitle = $subMenuTitle;
        return $this;
    }

    /**
     * Returns the icon to display in the top level menu
     *
     * @return string
     */
    final public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Sets the new icon if object is not locked
     *
     * @param  string $icon The new icon
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function setIcon(string $icon): self
    {
        $this->checkLock();

        $this->icon = $icon;
        return $this;
    }
}
