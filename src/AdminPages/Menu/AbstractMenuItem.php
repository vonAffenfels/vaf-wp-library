<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages\Menu;

use Closure;
use InvalidArgumentException;
use VAF\WP\Library\AdminPages\Renderer\AbstractRenderer;
use VAF\WP\Library\Exceptions\ObjectIsLocked;
use VAF\WP\Library\IsImmutable;

abstract class AbstractMenuItem
{
    use IsImmutable;

    /**
     * @var string Key of the new menu item (will be part of the slug)
     */
    private string $key;

    /**
     * @var string Title inside the menu
     */
    private string $menuTitle;

    /**
     * @var string Title of the page
     */
    private string $pageTitle;

    /**
     * @var int|null Position inside the menu (see Wordpress documentation for add_menu_page())
     */
    private ?int $position;

    /**
     * @var Closure|null Function to configure the page renderer
     */
    private ?Closure $configureFunc;

    /**
     * @var string Classname for the page renderer
     */
    private string $rendererClass;

    /**
     * Creates a new menu item
     *
     * @param string $key Key of the new menu item (will be part of the slug)
     * @param string $menuTitle Title inside the menu
     * @param string $rendererClass Classname for the page renderer
     * @param Closure|null $configureFunc Function to configure the page renderer
     */
    public function __construct(
        string $key,
        string $menuTitle,
        string $rendererClass = '',
        ?Closure $configureFunc = null
    ) {
        $this->key = sanitize_key($key);
        $this->menuTitle = $menuTitle;

        if (!is_subclass_of($rendererClass, AbstractRenderer::class)) {
            throw new InvalidArgumentException(sprintf(
                'Parameter "$rendererClass" must be a classname that extends %s. Class %s provided.',
                AbstractRenderer::class,
                $rendererClass
            ));
        }

        $this->rendererClass = $rendererClass;
        $this->configureFunc = $configureFunc;

        $this->pageTitle = $menuTitle;
        $this->position = null;
    }

    /**
     * Returns the configure function for the page renderer
     *
     * @return Closure
     */
    final public function getConfigureFunc(): Closure
    {
        return $this->configureFunc;
    }

    /**
     * Returns the classname for the page renderer
     *
     * @return string
     */
    final public function getRendererClass(): string
    {
        return $this->rendererClass;
    }

    /**
     * Returns the key of the menu
     *
     * @return string
     */
    final public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns the title inside the menu
     *
     * @return string
     */
    final public function getMenuTitle(): string
    {
        return $this->menuTitle;
    }

    /**
     * Returns the page title
     *
     * @return string
     */
    final public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * Sets the page title if object is not already locked
     *
     * @param  string $pageTitle The new page title
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
     * Returns the position of the menu item
     *
     * @return int|null
     */
    final public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Sets the position of the menu item if not already locked
     *
     * @param  int $position The new position
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
     * Representation of the object as string
     * Required for the trait IsImmutable
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("[MenuItem %s - %s]", $this->key, $this->menuTitle);
    }
}
