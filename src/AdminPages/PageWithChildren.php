<?php

namespace VAF\WP\Library\AdminPages;

use InvalidArgumentException;

class PageWithChildren extends AbstractAdminPage
{
    private array $children = [];

    public function registerChild(AbstractAdminPage $child): self
    {
        if ($child instanceof PageWithChildren) {
            throw new InvalidArgumentException('Cannot have admin pages with multiple levels (only one submenu is allowed)');
        }

        $this->children[] = $child;

        return $this;
    }

    /**
     * @return AbstractAdminPage[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function render(): string
    {
        return 'I AM A PAGE WITH CHILDREN';
    }
}
