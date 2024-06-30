<?php

namespace app;

use Bramus\Router\Router;
use Closure;

class Route
{
    private static Router $router;

    /**
     * Initializes the router.
     */
    public static function init(): void
    {
        self::$router = new Router();
    }

    /**
     * Registers a GET route with the router.
     *
     * @param string $uri The URI pattern that the route matches.
     * @param string|array $handler The handler that gets executed when the route is matched.
     */
    public static function get(string $uri, string|array|Closure $handler): void
    {
        self::$router->get($uri, self::handle($handler));
    }

    /**
     * Registers a POST route with the router.
     *
     * @param string $uri The URI pattern that the route matches.
     * @param string|array $handler The handler that gets executed when the route is matched.
     */
    public static function post(string $uri, string|array $handler): void
    {
        self::$router->post($uri, self::handle($handler));
    }

    /**
     * Registers a PUT route with the router.
     *
     * @param string $uri The URI pattern that the route matches.
     * @param string|array $handler The handler that gets executed when the route is matched.
     */
    public static function put(string $uri, string|array $handler): void
    {
        self::$router->post($uri, self::handle($handler));
    }

    /**
     * Registers a PATCH route with the router.
     *
     * @param string $uri The URI pattern that the route matches.
     * @param string|array $handler The handler that gets executed when the route is matched.
     */
    public static function patch(string $uri, string|array $handler): void
    {
        self::$router->post($uri, self::handle($handler));
    }

    /**
     * Registers a DELETE route with the router.
     *
     * @param string $uri The URI pattern that the route matches.
     * @param string|array $handler The handler that gets executed when the route is matched.
     */
    public static function delete(string $uri, string|array $handler): void
    {
        self::$router->post($uri, self::handle($handler));
    }

    /**
     * Handles the route handler.
     *
     * @param array|string $handler The handler that gets executed when the route is matched.
     * @return Closure The closure that wraps the handler.
     */
    private static function handle(array|string|Closure $handler): Closure
    {
        return function (...$params) use ($handler) {
            if (is_array($handler)) {
                [$controller, $method] = $handler;
                $controllerInstance = new $controller();

                return call_user_func_array([$controllerInstance, $method], $params);
            }

            return call_user_func($handler, ...$params);
        };
    }

    /**
     * Runs the router.
     */
    public static function run(): void
    {
        self::$router->run();
    }
}