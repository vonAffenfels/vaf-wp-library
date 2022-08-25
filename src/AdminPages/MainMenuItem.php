<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Exceptions\ObjectIsLocked;

final class MainMenuItem extends AbstractMenuItem
{
    /**
     * @var ChildMenuItem[]
     */
    private array $children = [];

    /**
     * @var string
     */
    private string $subMenuTitle;

    /**
     * @var string
     */
    private string $icon;

    final public function __construct(string $key, string $menuTitle)
    {
        parent::__construct($key, $menuTitle);

        $this->subMenuTitle = $menuTitle;
        $this->icon = '';
    }

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
     * @param ChildMenuItem $child
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function addChild(ChildMenuItem $child): self
    {
        $this->checkLock();

        $child->lockObject();
        $this->children[] = $child;
        return $this;
    }

    /**
     * @return string
     */
    final public function getSubMenuTitle(): string
    {
        return $this->subMenuTitle;
    }

    /**
     * @param string $subMenuTitle
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function setSubMenuTitle(string $subMenuTitle): self
    {
        $this->checkLock();

        $this->subMenuTitle = $subMenuTitle;
        return $this;
    }

    /**
     * @return string
     */
    final public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function setIcon(string $icon): self
    {
        $this->checkLock();

        $this->icon = $icon;
        return $this;
    }
}
