<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class MissingSettingKey extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $request, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Missing setting key in request! Only got "%s"',
            $plugin->getPluginSlug(),
            $request
        );
        parent::__construct($message, 0, $previous);
    }
}
