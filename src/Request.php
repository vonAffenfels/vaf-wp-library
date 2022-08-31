<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library;

final class Request
{
    public const TYPE_ALL = 0;
    public const TYPE_GET = 1;
    public const TYPE_POST = 2;
    public const TYPE_SERVER = 3;

    private static ?Request $instance = null;

    private array $get;
    private array $post;
    private array $server;

    final private function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    final static public function getInstance(): Request
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    final public function getParams(int $type = self::TYPE_ALL): array
    {
        switch ($type) {
            case self::TYPE_ALL:
                return array_merge($this->get, $this->post, $this->server);

            case self::TYPE_GET:
                return $this->get;

            case self::TYPE_POST:
                return $this->post;

            case self::TYPE_SERVER:
                return $this->server;
        }

        return [];
    }

    final public function getParam(string $key, int $type = self::TYPE_ALL, $default = null)
    {
        $params = $this->getParams($type);

        return $params[$key] ?? $default;
    }

    /**
     * Returns if request is an ajax request.
     * @return bool
     */
    final public function isAjaxRequest(): bool
    {
        return strtolower($this->getParam('HTTP_X_REQUESTED_WITH', self::TYPE_SERVER, '')) == 'xmlhttprequest';
    }

    /**
     * Returns if request is a post request.
     * @return bool
     */
    final public function isPost(): bool
    {
        return $this->getParam('REQUEST_METHOD', self::TYPE_SERVER, '') == 'POST';
    }

    /**
     * Returns if request is a get request.
     * @return bool
     */
    final public function isGet(): bool
    {
        return $this->getParam('REQUEST_METHOD', self::TYPE_SERVER, '') == 'GET';
    }

    /**
     * Returns true if request is a https request.
     * @return bool
     */
    final public function isSsl(): bool
    {
        return $this->getParam('HTTPS', self::TYPE_SERVER, '') == 'on';
    }

    /**
     * Returns true if the browser that has sent the request supports WebP
     * @return bool
     */
    final public function supportsWebP(): bool
    {
        return strpos($this->getParam('HTTP_ACCEPT', self::TYPE_SERVER, ''), 'image/webp') !== false;
    }
}
