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

namespace App\Presentation\Router;

/**
 * RouteFactory
 *
 * The default implementation of the AbstractRouteFactory
 */
class RouteFactory extends AbstractRouteFactory
{
    /**
     * Constants
     */

    /**
     * The value given to path's when they are entered as null values
     *
     * @var string
     */
    public const NULL_PATH_VALUE = '*';


    /**
     * Methods
     */

    /**
     * Check if the path is null or equal to our match-all, null-like value
     *
     * @param mixed $path
     * @return boolean
     */
    protected function pathIsNull($path)
    {
        return (static::NULL_PATH_VALUE === $path || null === $path);
    }

    /**
     * Quick check to see whether or not to count the route
     * as a match when counting total matches
     *
     * @param string $path
     * @return boolean
     */
    protected function shouldPathStringCauseRouteMatch($path)
    {
        // Only consider a request to be matched when not using 'matchall'
        return !$this->pathIsNull($path);
    }

    /**
     * Pre-process a path string
     *
     * This method wraps the path string in a regular expression syntax baesd
     * on whether the string is a catch-all or custom regular expression.
     * It also adds the namespace in a specific part, based on the style of expression
     *
     * @param string $path
     * @return string
     */
    protected function preprocessPathString($path)
    {
        // If the path is null, make sure to give it our match-all value
        $path = (null === $path) ? static::NULL_PATH_VALUE : (string) $path;

        // If a custom regular expression (or negated custom regex)
        if ($this->namespace &&
            (isset($path[0]) && $path[0] === '@') ||
            (isset($path[0]) && $path[0] === '!' && isset($path[1]) && $path[1] === '@')
        ) {
            // Is it negated?
            if ($path[0] === '!') {
                $negate = true;
                $path = substr($path, 2);
            } else {
                $negate = false;
                $path = substr($path, 1);
            }

            // Regex anchored to front of string
            if ($path[0] === '^') {
                $path = substr($path, 1);
            } else {
                $path = '.*' . $path;
            }

            if ($negate) {
                $path = '@^' . $this->namespace . '(?!' . $path . ')';
            } else {
                $path = '@^' . $this->namespace . $path;
            }
        } elseif ($this->namespace && $this->pathIsNull($path)) {
            // Empty route with namespace is a match-all
            $path = '@^' . $this->namespace . '(/|$)';
        } else {
            // Just prepend our namespace
            $path = $this->namespace . $path;
        }

        return $path;
    }

    /**
     * Build a Route instance
     *
     * @param callable $callback    Callable callback method to execute on route match
     * @param string $path          Route URI path to match
     * @param string|array $method  HTTP Method to match
     * @param boolean $count_match  Whether or not to count the route as a match when counting total matches
     * @param string $name          The name of the route
     * @return Route
     */
    public function build($callback, $path = null, $method = null, $count_match = true, $name = null)
    {
        return new Route(
            $callback,
            $this->preprocessPathString($path),
            $method,
            $this->shouldPathStringCauseRouteMatch($path) // Ignore the $count_match boolean that they passed
        );
    }
}
