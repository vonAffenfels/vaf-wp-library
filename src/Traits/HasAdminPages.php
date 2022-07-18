<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Traits;

use VAF\WP\Library\AdminPages\AbstractAdminPage;
use VAF\WP\Library\AdminPages\PageWithChildren;
use VAF\WP\Library\Traits\Internal\HasTemplates;

trait HasAdminPages
{
    use HasTemplates;

    final protected function startAdminPages(): void
    {
        add_filter('admin_menu', function () {
            $adminPage = $this->getAdminPage();

            $slug = $this->getSlugNamespace() . '-' . $adminPage->getSlug();

            add_menu_page(
                $adminPage->getPageTitle(),
                $adminPage->getMenuTitle(),
                $adminPage->getCapability(),
                $slug,
                function () use ($adminPage) {
                    echo $adminPage->render();
                },
                $adminPage->getIcon(),
                $adminPage->getPosition()
            );

            if ($adminPage instanceof PageWithChildren) {
                foreach ($adminPage->getChildren() as $child) {
                    add_submenu_page(
                        $slug,
                        $child->getPageTitle(),
                        $child->getMenuTitle(),
                        $child->getCapability(),
                        $slug . '-' . $child->getSlug(),
                        function () use ($child) {
                            echo $child->render();
                        },
                        $child->getPosition()
                    );
                }
            }
        });
    }

    abstract protected function getAdminPage(): AbstractAdminPage;

    abstract protected function getSlugNamespace(): string;
}
