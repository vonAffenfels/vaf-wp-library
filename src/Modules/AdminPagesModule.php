<?php

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\AdminPages\AdminPage;
use VAF\WP\Library\Exceptions\Module\AdminPage\InvalidAdminPageClass;
use VAF\WP\Library\Exceptions\Module\AdminPage\ParentMenuNotFound;

final class AdminPagesModule extends AbstractHookModule
{
    /**
     * @var array List of all adminpage classes
     */
    private array $adminPages = [];

    private static array $adminPageSlugMapper = [];

    /**
     * @var AdminPage[]
     */
    private array $laterChildMenus = [];

    /**
     * Returns a callable that is run to configure the module
     *
     * @param  array $adminPages
     * @return Closure
     */
    final public static function configure(array $adminPages): Closure
    {
        return function (AdminPagesModule $module) use ($adminPages) {
            foreach ($adminPages as $adminPage) {
                if (!is_subclass_of($adminPage, AdminPage::class)) {
                    throw new InvalidAdminPageClass($this->getPlugin(), $adminPage);
                }

                $module->adminPages[] = $adminPage;
            }
        };
    }

    /**
     * Register all hooks needed
     *
     * @return Closure[]
     */
    final protected function getHooks(): array
    {
        return [
            'admin_menu' => function () {
                $this->loadPages();
            }
        ];
    }

    /**
     * @return void
     * @throws ParentMenuNotFound
     */
    final private function loadPages(): void
    {
        foreach ($this->adminPages as $class) {
            $adminPageObj = new $class($this->getPlugin());

            $this->registerMenuItem($adminPageObj);
        }
    }

    /**
     * @param  AdminPage $page
     * @return void
     * @throws ParentMenuNotFound
     */
    final private function registerMenuItem(AdminPage $page): void
    {
        $menuItem = $page->getMenu();
        $menuItem->lockObject();
        $parent = $menuItem->getParent();

        $slugPrefix = $this->getPlugin()->getPluginSlug() . '-';

        if (is_null($parent)) {
            // We are registering a main menu item

            add_menu_page(
                $page->getTitle(),
                $menuItem->getMenuTitle(),
                'manage_options',
                $slugPrefix . $menuItem->getSlug(),
                function () use ($page) {
                    echo sprintf('Page %s - Menu Item %s', $page->getTitle(), $page->getMenu());
                },
                $menuItem->getIcon(),
                $menuItem->getPosition()
            );

            // Add atleast one child to give the option to name the submenu item differently
            // when having children
            add_submenu_page(
                $slugPrefix . $menuItem->getSlug(),
                $page->getTitle(),
                $menuItem->getSubMenuTitle(),
                'manage_options',
                $slugPrefix . $menuItem->getSlug(),
                '__return_false',
                null
            );

            self::$adminPageSlugMapper[get_class($page)] = $slugPrefix . $menuItem->getSlug();
        } else {
            // We will add a child
            // First we will check if parent exists
            if (isset(self::$adminPageSlugMapper[$parent])) {
                $parent = self::$adminPageSlugMapper[$parent];
            }

            if (!$this->hasParentMenu($parent)) {
                throw new ParentMenuNotFound($this->getPlugin(), $parent);
            }

            add_submenu_page(
                $parent,
                $page->getTitle(),
                $menuItem->getMenuTitle(),
                'manage_options',
                $slugPrefix . $menuItem->getSlug(),
                function () use ($page) {
                    echo sprintf('Page %s - Menu Item %s', $page->getTitle(), $page->getMenu());
                },
                $menuItem->getPosition()
            );
        }
    }

    /**
     * Function to check if the slug provided is already registered as a top level menu item
     *
     * @param string $slug
     * @return bool
     */
    final private function hasParentMenu(string $slug): bool
    {
        global $menu;

        $parentMenuArr = array_filter($menu, function (array $menuItem) use ($slug): bool {
            return $menuItem[2] == $slug;
        });

        return !empty($parentMenuArr);
    }
}
