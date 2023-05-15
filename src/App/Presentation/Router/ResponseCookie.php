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
 * ResponseCookie
 *
 * Class to represent an HTTP response cookie
 */
class ResponseCookie
{
    /**
     * Class properties
     */

    /**
     * The name of the cookie
     *
     * @var string
     */
    protected $name;

    /**
     * The string "value" of the cookie
     *
     * @var string
     */
    protected $value;

    /**
     * The date/time that the cookie should expire
     *
     * Represented by a Unix "Timestamp"
     *
     * @var int
     */
    protected $expire;

    /**
     * The path on the server that the cookie will
     * be available on
     *
     * @var string
     */
    protected $path;

    /**
     * The domain that the cookie is available to
     *
     * @var string
     */
    protected $domain;

    /**
     * Whether the cookie should only be transferred
     * over an HTTPS connection or not
     *
     * @var boolean
     */
    protected $secure;

    /**
     * Whether the cookie will be available through HTTP
     * only (not available to be accessed through
     * client-side scripting languages like JavaScript)
     *
     * @var boolean
     */
    protected $http_only;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param string  $name         The name of the cookie
     * @param string  $value        The value to set the cookie with
     * @param int     $expire       The time that the cookie should expire
     * @param string  $path         The path of which to restrict the cookie
     * @param string  $domain       The domain of which to restrict the cookie
     * @param boolean $secure       Flag of whether the cookie should only be sent over a HTTPS connection
     * @param boolean $http_only    Flag of whether the cookie should only be accessible over the HTTP protocol
     */
    public function __construct(
        $name,
        $value = null,
        $expire = null,
        $path = null,
        $domain = null,
        $secure = false,
        $http_only = false
    ) {
        // Initialize our properties
        $this->setName($name);
        $this->setValue($value);
        $this->setExpire($expire);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure($secure);
        $this->setHttpOnly($http_only);
    }

    /**
     * Gets the cookie's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the cookie's name
     *
     * @param string $name
     * @return ResponseCookie
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Gets the cookie's value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the cookie's value
     *
     * @param string $value
     * @return ResponseCookie
     */
    public function setValue($value)
    {
        if (null !== $value) {
            $this->value = (string) $value;
        } else {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * Gets the cookie's expire time
     *
     * @return int
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Sets the cookie's expire time
     *
     * The time should be an integer
     * representing a Unix timestamp
     *
     * @param int $expire
     * @return ResponseCookie
     */
    public function setExpire($expire)
    {
        if (null !== $expire) {
            $this->expire = (int) $expire;
        } else {
            $this->expire = $expire;
        }

        return $this;
    }

    /**
     * Gets the cookie's path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the cookie's path
     *
     * @param string $path
     * @return ResponseCookie
     */
    public function setPath($path)
    {
        if (null !== $path) {
            $this->path = (string) $path;
        } else {
            $this->path = $path;
        }

        return $this;
    }

    /**
     * Gets the cookie's domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the cookie's domain
     *
     * @param string $domain
     * @return ResponseCookie
     */
    public function setDomain($domain)
    {
        if (null !== $domain) {
            $this->domain = (string) $domain;
        } else {
            $this->domain = '';
        }

        return $this;
    }

    /**
     * Gets the cookie's secure only flag
     *
     * @return boolean
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Sets the cookie's secure only flag
     *
     * @param boolean $secure
     * @return ResponseCookie
     */
    public function setSecure($secure)
    {
        $this->secure = (bool) $secure;

        return $this;
    }

    /**
     * Gets the cookie's HTTP only flag
     *
     * @return boolean
     */
    public function getHttpOnly()
    {
        return $this->http_only;
    }

    /**
     * Sets the cookie's HTTP only flag
     *
     * @param boolean $http_only
     * @return ResponseCookie
     */
    public function setHttpOnly($http_only)
    {
        $this->http_only = (bool) $http_only;

        return $this;
    }
}
