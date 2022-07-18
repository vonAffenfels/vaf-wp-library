<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use InvalidArgumentException;
use VAF\WP\Library\AdminPages\AbstractAdminPage;
use VAF\WP\Library\AdminPages\PageWithChildren;

final class AdminPages extends AbstractFeature
{
    final public function start(): self
    {
        /** @var AbstractAdminPage $adminPage */
        $adminPage = $this->getParameter('adminPage');
        if (is_null($adminPage)) {
            throw new InvalidArgumentException('Feature "AdminPages" not correctly configured!');
        }

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

    final protected function getParameters(): array
    {
        return [
            'adminPage' => [
                'required' => true
            ]
        ];
    }
}
