<?php

namespace VAF\WP\Library\AdminPages;

final class MenuItem
{
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_SUBMENU_PARENT = 'submenu-parent';
    public const TYPE_SUBMENU_CHILD = 'submenu-child';
    public const TYPE_PREDEFINED_SUBMENU = 'predefined-submenu';

    private string $type;

    private array $children = [];

    final private function __construct()
    {
    }

    final public function getType(): string
    {
        return $this->type;
    }

    final public static function createSimpleMenuItem(
        string $key,
        string $menuTitle,
        ?string $icon = null,
        ?string $pageTitle = null,
        ?int $position = null
    ): MenuItem {
        $menuItem = new MenuItem();
        $menuItem->type = self::TYPE_SIMPLE;

        return $menuItem;
    }

    final public static function createSubMenuItem(
        string $key,
        string $menuTitle,
        array $children,
        ?string $icon = null,
        ?int $position = null
    ): MenuItem {
        $menuItem = new MenuItem();
        $menuItem->type = self::TYPE_SUBMENU_PARENT;

        return $menuItem;
    }

    final public static function createSubMenuChildItem(
        string $key,
        string $menuTitle,
        ?string $icon = null,
        ?string $pageTitle = null
    ): MenuItem {
        $menuItem = new MenuItem();
        $menuItem->type = self::TYPE_SUBMENU_CHILD;

        return $menuItem;
    }

    final public static function createPredefinedChildMenuItem(
        string $key,
        string $menuTitle,
        ?string $icon = null,
        ?string $pageTitle = null
    ): MenuItem {
        $menuItem = new MenuItem();
        $menuItem->type = self::TYPE_PREDEFINED_SUBMENU;

        return $menuItem;
    }
}
