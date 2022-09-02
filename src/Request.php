<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library;

/**
 * Class to represent a request object
 * Has helper functions to determine the request method and also to access parameters
 */
final class Request
{
    /**
     * List of parameter types to access
     */
    public const TYPE_ALL = 0;
    public const TYPE_GET = 1;
    public const TYPE_POST = 2;
    public const TYPE_SERVER = 3;

    /**
     * @var Request|null Singleton object
     */
    private static ?Request $instance = null;

    /**
     * @var array All available GET parameters
     */
    private array $get;

    /**
     * @var array All available POST parameters
     */
    private array $post;

    /**
     * @var array All available server parameters
     */
    private array $server;

    /**
     * Constructor
     */
    final private function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    /**
     * Returns the singleton instance
     * If none exist it creates one
     *
     * @return Request
     */
    final public static function getInstance(): Request
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns all parameters for a specific type
     *
     * @param int $type
     * @return array
     */
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

    /**
     * Returns the requested parameter
     *
     * @param string $key
     * @param int $type
     * @param $default
     * @return mixed|null
     */
    final public function getParam(string $key, int $type = self::TYPE_ALL, $default = null)
    {
        $params = $this->getParams($type);

        return $params[$key] ?? $default;
    }

    /**
     * Returns true if request is an ajax request.
     *
     * @return bool
     */
    final public function isAjaxRequest(): bool
    {
        return strtolower($this->getParam('HTTP_X_REQUESTED_WITH', self::TYPE_SERVER, '')) == 'xmlhttprequest';
    }

    /**
     * Returns true if request is a POST request.
     *
     * @return bool
     */
    final public function isPost(): bool
    {
        return $this->getParam('REQUEST_METHOD', self::TYPE_SERVER, '') == 'POST';
    }

    /**
     * Returns true if request is a GET request.
     *
     * @return bool
     */
    final public function isGet(): bool
    {
        return $this->getParam('REQUEST_METHOD', self::TYPE_SERVER, '') == 'GET';
    }

    /**
     * Returns true if request is a HTTPS request.
     *
     * @return bool
     */
    final public function isSsl(): bool
    {
        return $this->getParam('HTTPS', self::TYPE_SERVER, '') == 'on';
    }

    /**
     * Returns true if the browser that has sent the request supports WebP
     *
     * @return bool
     */
    final public function supportsWebP(): bool
    {
        return strpos($this->getParam('HTTP_ACCEPT', self::TYPE_SERVER, ''), 'image/webp') !== false;
    }
}
