<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages\Menu;

use VAF\WP\Library\Exceptions\ObjectIsLocked;

final class MainMenuItem extends AbstractMenuItem
{
    use HasChildren;

    /**
     * @var string
     */
    private string $subMenuTitle;

    /**
     * @var bool
     */
    private bool $isPredefined = false;

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
     * @return string
     */
    final public function getSubMenuTitle(): string
    {
        return $this->subMenuTitle;
    }

    /**
     * @param  string $subMenuTitle
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
     * @param  string $icon
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
