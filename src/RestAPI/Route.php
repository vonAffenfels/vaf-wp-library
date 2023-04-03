<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\RestAPI;

use Exception;
use WP_REST_Request;

/**
 * Base class for a route for the Rest API
 */
abstract class Route
{
    /**
     * List of constants for HTTP methods supported by the Rest API
     */
    protected const METHOD_GET = 'GET';
    protected const METHOD_POST = 'POST';
    protected const METHOD_DELETE = 'DELETE';
    protected const METHOD_PUT = 'PUT';

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
     * Returns wether the current user has the permission to use this route
     *
     * @return bool
     */
    abstract public function checkPermission(): bool;

    /**
     * Handler of the route
     *
     * @param  WP_REST_Request $request
     * @return array|null
     */
    abstract protected function handle(WP_REST_Request $request): ?array;

    /**
     * Function to handle the request and the response
     *
     * @param  WP_REST_Request $request
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
                $return = array_merge([
                    'success' => true
                ], $data);
            }
        } catch (Exception $e) {
            $return['success'] = false;
            $return['message'] = $e->getMessage();
        }

        return $return;
    }
}
