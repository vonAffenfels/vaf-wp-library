<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library;

use Exception;
use VAF\WP\Library\Traits\Internal\HasPlugin;
use WP_REST_Request;

abstract class RestRoute
{
    use HasPlugin;

    protected const METHOD_GET = 'GET';
    protected const METHOD_POST = 'POST';
    protected const METHOD_DELETE = 'DELETE';
    protected const METHOD_PUT = 'PUT';

    abstract public function getMethod(): string;

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

    abstract public function checkPermission(): bool;

    abstract protected function handle(WP_REST_Request $request): ?array;

    final public function handleRestRequest(WP_REST_Request $request): array
    {
        $return = [
            'success' => false
        ];

        try {
            $data = $this->handle($request);
            if ($data) {
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
