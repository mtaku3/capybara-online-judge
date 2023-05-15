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

use BadMethodCallException;
use App\Presentation\Router\Exceptions\DuplicateServiceException;
use App\Presentation\Router\Exceptions\UnknownServiceException;

/**
 * App
 */
class App
{
    /**
     * Class properties
     */

    /**
     * The array of app services
     *
     * @var array
     */
    protected $services = array();

    /**
     * Magic "__get" method
     *
     * Allows the ability to arbitrarily request a service from this instance
     * while treating it as an instance property
     *
     * This checks the lazy service register and automatically calls the registered
     * service method
     *
     * @param string $name              The name of the service
     * @throws UnknownServiceException  If a non-registered service is attempted to fetched
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->services[$name])) {
            throw new UnknownServiceException('Unknown service '. $name);
        }
        $service = $this->services[$name];

        return $service();
    }

    /**
     * Magic "__call" method
     *
     * Allows the ability to arbitrarily call a property as a callable method
     * Allow callbacks to be assigned as properties and called like normal methods
     *
     * @param callable $method          The callable method to execute
     * @param array $args               The argument array to pass to our callback
     * @throws BadMethodCallException   If a non-registered method is attempted to be called
     * @return void
     */
    public function __call($method, $args)
    {
        if (!isset($this->services[$method]) || !is_callable($this->services[$method])) {
            throw new BadMethodCallException('Unknown method '. $method .'()');
        }

        return call_user_func_array($this->services[$method], $args);
    }

    /**
     * Register a lazy service
     *
     * @param string $name                  The name of the service
     * @param callable $closure             The callable function to execute when requesting our service
     * @throws DuplicateServiceException    If an attempt is made to register two services with the same name
     * @return mixed
     */
    public function register($name, $closure)
    {
        if (isset($this->services[$name])) {
            throw new DuplicateServiceException('A service is already registered under '. $name);
        }

        $this->services[$name] = function () use ($closure) {
            static $instance;
            if (null === $instance) {
                $instance = $closure();
            }

            return $instance;
        };
    }
}
