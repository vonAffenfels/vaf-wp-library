<?php

namespace VAF\WP\Library\AdminPages\Renderer;

use Closure;
use VAF\WP\Library\Exceptions\Module\AdminPage\TabAlreadyRegistered;
use VAF\WP\Library\Request;

abstract class TabbedPageRenderer extends AbstractRenderer
{
    private array $tabs = [];

    /**
     * @param  string $key
     * @param  string $title
     * @param  string $rendererClass
     * @param  Closure|null $configureFunc
     * @return void
     * @throws TabAlreadyRegistered
     */
    final public function registerTab(string $key, string $title, string $rendererClass, ?Closure $configureFunc)
    {
        if (isset($tabs[$key])) {
            throw new TabAlreadyRegistered($this->getPlugin(), $key, $title);
        }

        $this->tabs[$key] = [
            'key' => $key,
            'title' => $title,
            'renderer' => $rendererClass,
            'configure' => $configureFunc
        ];
    }

    abstract protected function init();

    final private function handleTab(string $tab)
    {
    }

    final public function render(): string
    {
        $this->init();

        if (empty($this->tabs)) {
            throw new Exception("No tabs registered!");
        }

        $request = Request::getInstance();
        $currentTab = $request->getParam('tab', Request::TYPE_GET, $this->tabs[0]['key']);

        $this->handleTab($currentTab);
    }
}
