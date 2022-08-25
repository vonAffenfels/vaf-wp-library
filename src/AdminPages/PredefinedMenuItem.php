<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\IsImmutable;

final class PredefinedMenuItem
{
    use IsImmutable;
    use HasChildren;

    /**
     * @var string
     */
    private string $slug;

    /**
     * @var string
     */
    private string $title;

    final public static function getMediaMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('upload.php', 'Media');
    }

    final public static function getCommentsMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('edit-comments.php', 'Comments');
    }

    final public static function getPostsMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('edit.php', 'Posts');
    }

    final public static function getPagesMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('edit.php?post_type=page', 'Pages');
    }

    final public static function getAppearanceMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('themes.php', 'Appearance');
    }

    final public static function getPluginsMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('plugins.php', 'Plugins');
    }

    final public static function getUsersMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('users.php', 'Users');
    }

    final public static function getToolsMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('tools.php', 'Tools');
    }

    final public static function getDashboardMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('index.php', 'Dashboard');
    }

    final public static function getSettingsMenuItem(): PredefinedMenuItem
    {
        return new PredefinedMenuItem('options-general.php', 'Settings');
    }

    /**
     * @param string $slug
     * @param string $title
     */
    final private function __construct(string $slug, string $title)
    {
        $this->slug = $slug;
        $this->title = $title;
    }

    /**
     * @return string
     */
    final public function __toString(): string
    {
        return sprintf("[PredefinedMenuItem %s]", $this->title);
    }

    final public function getSlug(): string
    {
        return $this->slug;
    }
}