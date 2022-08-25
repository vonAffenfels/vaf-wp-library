<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Modules;

use InvalidArgumentException;
use VAF\WP\Library\AdminPages\MainMenuItem;
use VAF\WP\Library\AdminPages\PredefinedMenuItem;

final class AdminPagesModule extends AbstractHookModule
{
    /**
     * @var MainMenuItem[]
     */
    private array $menuItems = [];

    /**
     * @param MainMenuItem[]|PredefinedMenuItem[] $menuItems
     * @return callable
     */
    final public static function configure(array $menuItems = []): callable
    {
        return function (AdminPagesModule $module) use ($menuItems) {
            $filteredMenuItems = array_filter($menuItems, function ($item) {
                return ($item instanceof MainMenuItem) || ($item instanceof PredefinedMenuItem);
            });

            if (count($filteredMenuItems) !== count($menuItems)) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Parameter %s of %s has to contain only objects of classes %s or %s",
                        '$menuItems',
                        AdminPagesModule::class,
                        MainMenuItem::class,
                        PredefinedMenuItem::class
                    )
                );
            }
            $module->menuItems = $menuItems;
        };
    }

    final protected function getHooks(): array
    {
        return [
            'admin_menu' => function () {
                foreach ($this->menuItems as $menuItem) {
                    switch (get_class($menuItem)) {
                        case MainMenuItem::class:
                            $this->registerMenuItem($menuItem);
                            break;

                        case PredefinedMenuItem::class:
                            $this->registerPredefinedMenuItem($menuItem);
                            break;
                    }
                }
            }
        ];
    }

    final private function registerMenuItem(MainMenuItem $menuItem): void
    {
        $menuSlug = $this->getPlugin()->getPluginSlug();

        $menuItem->lockObject();
        $menuSlug = $menuSlug . '-' . $menuItem->getKey();

        add_menu_page(
            $menuItem->getPageTitle(),
            $menuItem->getMenuTitle(),
            'manage_options',
            $menuSlug,
            function () use ($menuItem) {
                echo $menuItem->getKey();
            },
            $menuItem->getIcon(),
            $menuItem->getPosition()
        );

        if ($menuItem->hasChildren()) {
            add_submenu_page(
                $menuSlug,
                $menuItem->getPageTitle(),
                $menuItem->getSubMenuTitle(),
                'manage_options',
                $menuSlug,
                '',
                $menuItem->getPosition()
            );

            foreach ($menuItem->getChildren() as $child) {
                $child->lockObject();

                add_submenu_page(
                    $menuSlug,
                    $child->getPageTitle(),
                    $child->getMenuTitle(),
                    'manage_options',
                    $menuSlug . '-' . $child->getKey(),
                    function () use ($child) {
                        echo "SUB MENU " . $child->getKey();
                    },
                    $child->getPosition()
                );
            }
        }
    }

    final private function registerPredefinedMenuItem(PredefinedMenuItem $menuItem): void
    {
        $menuSlug = $this->getPlugin()->getPluginSlug();

        if ($menuItem->hasChildren()) {
            foreach ($menuItem->getChildren() as $child) {
                $child->lockObject();

                add_submenu_page(
                    $menuItem->getSlug(),
                    $child->getPageTitle(),
                    $child->getMenuTitle(),
                    'manage_options',
                    $menuSlug . '-' . $child->getKey(),
                    function () use ($child) {
                        echo "SUB MENU " . $child->getKey();
                    },
                    $child->getPosition()
                );
            }
        }
    }
}
