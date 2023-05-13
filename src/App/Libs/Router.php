<?php

declare(strict_types=1);

namespace App\Libs;

class Router
{
    /**
     * @var bool
     */
    private static bool $HasRouted = false;

    /**
     * @var array
     */
    private array $middlewares;

    /** @return bool  */
    public static function GetHasRouted(): bool
    {
        return self::$HasRouted;
    }

    /**
     * @param array $middlewares
     * @return void
     */
    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @param callable $func
     * @return Router
     */
    public function use(callable $func): self
    {
        return new Router($this->middlewares + [$func]);
    }

    /**
     * @param string $route
     * @param callable $callback
     * @return void
     */
    public function get(string $route, callable $callback): void
    {
        if (self::$HasRouted) {
            return;
        }

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }

        self::on($route, $callback);
    }

    /**
     * @param string $route
     * @param callable $callback
     * @return void
     */
    public function post(string $route, callable $callback): void
    {
        if (self::$HasRouted) {
            return;
        }

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }

        self::on($route, $callback);
    }

    /**
     * @param string $regex
     * @param callable $cb
     * @return void
     */
    public function on(string $regex, callable $cb): void
    {
        $params = $_SERVER['REQUEST_URI'];
        $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
        $regex = str_replace('/', '\/', $regex);
        $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);

        if ($is_match) {
            self::$HasRouted = true;

            array_shift($matches);
            $params = array_map(function ($param) {
                return $param[0];
            }, $matches);
            $request = new Request($params);
            $response = new Response();

            foreach ($this->middlewares as $middleware) {
                if (!$middleware($request, $response)) {
                    return;
                }
            }

            $cb($request, $response);
        }
    }
}
