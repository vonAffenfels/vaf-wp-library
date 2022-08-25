<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Modules;

use InvalidArgumentException;
use VAF\WP\Library\AdminPages\MainMenuItem;

final class AdminPagesModule extends AbstractHookModule
{
    /**
     * @var MainMenuItem[]
     */
    private array $menuItems = [];

    /**
     * @param MainMenuItem[] $menuItems
     * @return callable
     */
    final public static function configure(array $menuItems): callable
    {
        return function (AdminPagesModule $module) use ($menuItems) {
            $filteredMenuItems = array_filter($menuItems, function ($item) {
                return $item instanceof MainMenuItem;
            });

            if (count($filteredMenuItems) !== count($menuItems)) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Parameter %s of %s has to contain only objects of class %s",
                        '$menuItems',
                        AdminPagesModule::class,
                        MainMenuItem::class
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
                $this->registerAdminMenuItems();
            }
        ];
    }

    final private function registerAdminMenuItems(): void
    {
        foreach ($this->menuItems as $menuItem) {
            $menuItem->lockObject();
            $menuSlug = $this->getPlugin()->getPluginSlug() . '-' . $menuItem->getKey();

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
    }
}
