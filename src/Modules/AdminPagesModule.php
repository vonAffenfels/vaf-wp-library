<?php

namespace VAF\WP\Library\Modules;

final class AdminPagesModule extends AbstractHookModule
{
    /**
     * @var AbstractMenuItem[]
     */
    private array $menuItems = [];

    /**
     * @param AbstractMenuItem[] $menuItems
     * @return callable
     */
    final public static function configure(array $menuItems): callable
    {
        return function (AdminPagesModule $module) use ($menuItems) {
            $this->menuItems = $menuItems;
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
        }
    }
}
