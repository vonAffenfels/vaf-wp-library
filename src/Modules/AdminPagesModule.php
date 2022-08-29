<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\AdminPages\Menu\ChildMenuItem;
use VAF\WP\Library\AdminPages\Menu\MainMenuItem;
use VAF\WP\Library\Exceptions\Module\AdminPage\ParentMenuNotFound;

final class AdminPagesModule extends AbstractHookModule
{
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

    /**
     * @var Closure Closure function that is run at the right time to register menu items
     */
    private Closure $menuItemCreator;

    /**
     * @param  Closure $menuItemCreator Closure function that is run at the right time to register menu items
     * @return callable
     */
    final public static function configure(Closure $menuItemCreator): Closure
    {
        return function (AdminPagesModule $module) use ($menuItemCreator) {
            $module->menuItemCreator = $menuItemCreator;
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
                call_user_func($this->menuItemCreator, $this);
            }
        ];
    }

    /**
     * Adds a new top level menu item with all children
     *
     * @param MainMenuItem $menuItem Menu item to add
     * @return void
     */
    final public function addMenuItem(MainMenuItem $menuItem): void
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
                echo "PARENT MENU " . $menuItem->getKey();
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
                '__return_false',
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

    /**
     * Adds a new child menu item to an existing menu item which our plugin didn't created
     *
     * @param string $slug Slug of the top level menu item
     * @param ChildMenuItem $child Child to add
     * @return void
     * @throws ParentMenuNotFound
     */
    final public function addChildToExisting(string $slug, ChildMenuItem $child): void
    {
        if (!$this->hasParentMenu($slug)) {
            throw new ParentMenuNotFound($this->getPlugin(), $slug);
        }

        $menuSlug = $this->getPlugin()->getPluginSlug();

        $child->lockObject();

        add_submenu_page(
            $slug,
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

    final private function renderPage(string $pageClass, ?Closure $configureFunc = null): void
    {
    }
}
