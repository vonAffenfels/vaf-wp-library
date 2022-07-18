<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Traits\Internal\HasTemplates;

class SettingsPage extends AbstractAdminPage
{
    public function render(): string
    {
        return 'I AM A SETTINGS PAGE';
    }
}
