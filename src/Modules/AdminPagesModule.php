<?php

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\AdminPages\AdminPage;
use VAF\WP\Library\Exceptions\Module\AdminPage\InvalidAdminPageClass;

final class AdminPagesModule extends AbstractHookModule
{
    /**
     * @var array List of all adminpage classes
     */
    private array $adminPages = [];

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
            }
        ];
    }
}
