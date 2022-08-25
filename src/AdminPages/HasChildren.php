<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Exceptions\ObjectIsLocked;

trait HasChildren
{
    /**
     * @var ChildMenuItem[]
     */
    private array $children = [];

    /**
     * @return bool
     */
    final public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * @return ChildMenuItem[]
     */
    final public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param  ChildMenuItem $child
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function addChild(ChildMenuItem $child): self
    {
        $this->checkLock();

        $this->children[] = $child;
        return $this;
    }
}
