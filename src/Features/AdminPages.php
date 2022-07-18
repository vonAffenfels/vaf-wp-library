<?php

namespace VAF\WP\Library\Features;

use VAF\WP\Library\AdminPages\AbstractAdminPage;
use VAF\WP\Library\AdminPages\PageWithChildren;

final class AdminPages extends AbstractFeature
{
    /**
     * @param AbstractAdminPage $adminPage
     * @return $this
     */
    final public function start(AbstractAdminPage $adminPage): self
    {
        add_filter('admin_menu', function () use ($adminPage) {
            $slug = $this->getPlugin()->getPluginName() . '-' . $adminPage->getSlug();

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

        return $this;
    }
}
