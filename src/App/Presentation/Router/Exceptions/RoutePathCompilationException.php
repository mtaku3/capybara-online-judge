<?php
/**
 * Copyright (c) 2010 Chris O'Hara <cohara87@gmail.com>
 * Copyright (c) 2023 mtaku3
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Presentation\Router\Exceptions;

use Exception;
use App\Presentation\Router\Route;
use RuntimeException;
use Throwable;

/**
 * RoutePathCompilationException
 *
 * Exception used for when a route's path fails to compile
 */
class RoutePathCompilationException extends RuntimeException implements RouterExceptionInterface
{
    /**
     * Constants
     */

    /**
     * The exception message format
     *
     * @type string
     */
    public const MESSAGE_FORMAT = 'Route failed to compile with path "%s".';

    /**
     * The extra failure message format
     *
     * @type string
     */
    public const FAILURE_MESSAGE_TITLE_FORMAT = 'Failed with message: "%s"';


    /**
     * Properties
     */

    /**
     * The route that failed to compile
     *
     * @type Route
     */
    protected $route;


    /**
     * Methods
     */

    /**
     * Create a RoutePathCompilationException from a route
     * and an optional previous exception
     *
     * TODO: Change the `$previous` parameter to type-hint against `Throwable`
     * once PHP 5.x support is no longer necessary.
     *
     * @param Route $route          The route that failed to compile
     * @param Exception|Throwable $previous   The previous exception
     * @return RoutePathCompilationException
     */
    public static function createFromRoute(Route $route, $previous = null)
    {
        $error = (null !== $previous) ? $previous->getMessage() : null;
        $code  = (null !== $previous) ? $previous->getCode() : null;

        $message = sprintf(static::MESSAGE_FORMAT, $route->getPath());
        $message .= ' '. sprintf(static::FAILURE_MESSAGE_TITLE_FORMAT, $error);

        $exception = new static($message, $code, $previous);
        $exception->setRoute($route);

        return $exception;
    }

    /**
     * Gets the value of route
     *
     * @sccess public
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the value of route
     *
     * @param Route The route that failed to compile
     * @sccess protected
     * @return RoutePathCompilationException
     */
    protected function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }
}
