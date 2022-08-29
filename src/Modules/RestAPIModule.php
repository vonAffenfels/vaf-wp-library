<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\Exceptions\Module\RestAPI\InvalidRouteClass;
use VAF\WP\Library\RestAPI\Route;
use WP_REST_Request;

final class RestAPIModule extends AbstractHookModule
{
    /**
     * Returns a callable that is run to configure the module
     *
     * @param  array  $routes
     * @param  string $restNamespace
     * @return callable
     */
    final public static function configure(array $routes, string $restNamespace): Closure
    {
        return function (RestAPIModule $module) use ($routes, $restNamespace) {
            $module->routes = $routes;
            $module->restNamespace = $restNamespace;
        };
    }

    /**
     * @var string[] Route classes to register
     */
    private array $routes = [];

    /**
     * @var string Namespace for the Rest API module
     */
    private string $restNamespace = '';

    /**
     * @return Closure[]
     */
    protected function getHooks(): array
    {
        return [
            'rest_api_init' => function () {
                foreach ($this->routes as $route) {
                    $this->registerRestRoute($route);
                }
            }
        ];
    }

    /**
     * @throws InvalidRouteClass
     */
    private function registerRestRoute(string $classname): void
    {
        if (!is_subclass_of($classname, Route::class)) {
            throw new InvalidRouteClass($this->getPlugin(), $classname);
        }

        /** @var Route $route */
        $route = new $classname();

        register_rest_route(
            $this->restNamespace,
            $route->getRoute(),
            [
                'methods' => $route->getMethod(),
                'callback' => function (WP_REST_Request $request) use ($route): array {
                    return $route->handleRestRequest($request);
                },
                'permission_callback' => function () use ($route): bool {
                    return $route->checkPermission();
                },
                'args' => $this->getArguments($route)
            ]
        );
    }

    /**
     * @param  Route $route
     * @return array
     */
    final private function getArguments(Route $route): array
    {
        $arguments = $route->getArguments();
        $return = [];

        foreach ($arguments as $argument => $config) {
            $sanitizeCallback = null;

            if (is_callable($config['sanitizeCallback'] ?? null)) {
                $sanitizeCallback = $config['sanitizeCallback'];
            } else {
                switch ($config['type'] ?? 'string') {
                    case 'string':
                        $sanitizeCallback = function ($value, WP_REST_Request $request, string $param): string {
                            return sanitize_text_field($value);
                        };
                        break;

                    case 'url':
                        $sanitizeCallback = function ($value, WP_REST_Request $request, string $param): string {
                            return sanitize_url($value);
                        };
                        break;
                }
            }


            $return[$argument] = [
                'required' => $config['required'] ?? false,
                'default' => $config['default'] ?? null
            ];

            if (!is_null($sanitizeCallback)) {
                $return[$argument]['sanitize_callback'] = $sanitizeCallback;
            }
        }

        return $return;
    }
}
