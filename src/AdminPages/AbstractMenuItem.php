<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Exceptions\ObjectIsLocked;
use VAF\WP\Library\IsImmutable;

abstract class AbstractMenuItem
{
    use IsImmutable;

    /**
     * @var string
     */
    private string $key;

    /**
     * @var string
     */
    private string $menuTitle;

    /**
     * @var string
     */
    private string $pageTitle;

    /**
     * @var int|null
     */
    private ?int $position;

    /**
     * @param string $key
     * @param string $menuTitle
     */
    public function __construct(string $key, string $menuTitle)
    {
        $this->key = sanitize_key($key);
        $this->menuTitle = $menuTitle;

        $this->pageTitle = $menuTitle;
        $this->position = null;
    }

    /**
     * @return string
     */
    final public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    final public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    /**
     * @return string
     */
    final public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * @param  string $pageTitle
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function setPageTitle(string $pageTitle): self
    {
        $this->checkLock();

        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * @return int|null
     */
    final public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param  int $position
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function setPosition(int $position): self
    {
        $this->checkLock();

        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("[MenuItem %s - %s]", $this->key, $this->menuTitle);
    }
}
