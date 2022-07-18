<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use VAF\WP\Library\AdminPages\AbstractAdminPage;
use VAF\WP\Library\AdminPages\PageWithChildren;
use VAF\WP\Library\Plugin;

final class AdminPages extends AbstractFeature
{
    public const FEATURE_NAME = 'adminPages';

    final public function __construct(Plugin $plugin, AbstractAdminPage $adminPage)
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
    }

    final public function getName(): string
    {
        return self::FEATURE_NAME;
    }
}
