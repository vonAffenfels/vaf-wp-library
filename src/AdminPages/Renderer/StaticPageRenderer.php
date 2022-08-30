<?php

namespace VAF\WP\Library\AdminPages\Renderer;

use Closure;
use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;

final class StaticPageRenderer extends AbstractRenderer
{
    /**
     * @var string Template to render
     */
    private string $template = '';

    /**
     * @var array Additional data to pass to the template
     */
    private array $context = [];

    final public static function configure(string $template, array $context = []): Closure
    {
        return function (StaticPageRenderer $pageRenderer) use ($template, $context) {
            $pageRenderer->template = $template;
            $pageRenderer->context = $context;
        };
    }

    /**
     * @return string
     * @throws NamespaceNotRegistered
     * @throws TemplateNotFound
     */
    public function render(): string
    {
        return $this->getPlugin()->renderTemplate($this->template, $this->context);
    }
}
