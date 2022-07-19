<?php

/**
 * @noinspection PhpUnused
 */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\RestAPI;

use Exception;
use VAF\WP\Library\Plugin;
use WP_REST_Request;

abstract class Route
{
    protected const METHOD_GET = 'GET';
    protected const METHOD_POST = 'POST';
    protected const METHOD_DELETE = 'DELETE';
    protected const METHOD_PUT = 'PUT';

    //<editor-fold desc="Abstract function definitions">
    /*********************************
     * Abstract function definitions *
     *********************************/

    /**
     * Should return the HTTP method the route is listening to
     *
     * @return string
     */
    abstract public function getMethod(): string;

    /**
     * Should return the route path of the URL
     *
     * @return string
     */
    abstract public function getRoute(): string;

    /**
     * Function should return all parameters
     *
     * ```
     *  return [
     *      'objectId' => [
     *          'required' => true,
     *          'type' => 'string'
     *      ],
     *      'url' => [
     *          'required' => false,
     *          'default' => null,
     *          'type' => 'url'
     *      ]
     *  ];
     * ```
     *
     * @return array
     */
    abstract public function getArguments(): array;

    /**
     * Returns weather the current user has the permission to use this route
     *
     * @return bool
     */
    abstract public function checkPermission(): bool;

    /**
     * Handling of the route
     *
     * @param WP_REST_Request $request
     * @return array|null
     */
    abstract protected function handle(WP_REST_Request $request): ?array;
    //</editor-fold>

    //<editor-fold desc="Plugin handling">
    /*******************
     * Plugin handling *
     *******************/

    private Plugin $plugin;

    final protected function getPlugin(): Plugin
    {
        return $this->plugin;
    }
    //</editor-fold>

    //<editor-fold desc="Constructor">
    /***************
     * Constructor *
     ***************/
    final public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    //</editor-fold>

    /**
     * @param WP_REST_Request $request
     * @return false[]
     */
    final public function handleRestRequest(WP_REST_Request $request): array
    {
        $return = [
            'success' => false
        ];

        try {
            $data = $this->handle($request);
            if (!is_null($data)) {
                $return = array_merge($data, [
                    'success' => true
                ]);
            }
        } catch (Exception $e) {
            $return['success'] = false;
            $return['message'] = $e->getMessage();
        }

        return $return;
    }
}
