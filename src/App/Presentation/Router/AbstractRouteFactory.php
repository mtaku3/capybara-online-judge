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
 * AbstractRouteFactory
 *
 * Abstract class for a factory for building new Route instances
 */
abstract class AbstractRouteFactory
{
    /**
     * Properties
     */

    /**
     * The namespace of which to collect the routes in
     * when matching, so you can define routes under a
     * common endpoint
     *
     * @var string
     */
    protected $namespace;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param string $namespace The initial namespace to set
     */
    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * Gets the value of namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the value of namespace
     *
     * @param string $namespace The namespace from which to collect the Routes under
     * @return AbstractRouteFactory
     */
    public function setNamespace($namespace)
    {
        $this->namespace = (string) $namespace;

        return $this;
    }

    /**
     * Append a namespace to the current namespace
     *
     * @param string $namespace The namespace from which to collect the Routes under
     * @return AbstractRouteFactory
     */
    public function appendNamespace($namespace)
    {
        $this->namespace .= (string) $namespace;

        return $this;
    }

    /**
     * Build factory method
     *
     * This method should be implemented to return a Route instance
     *
     * @param callable $callback    Callable callback method to execute on route match
     * @param string $path          Route URI path to match
     * @param string|array $method  HTTP Method to match
     * @param boolean $count_match  Whether or not to count the route as a match when counting total matches
     * @param string $name          The name of the route
     * @return Route
     */
    abstract public function build($callback, $path = null, $method = null, $count_match = true, $name = null);
}
