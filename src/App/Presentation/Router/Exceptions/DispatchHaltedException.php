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

use RuntimeException;

/**
 * DispatchHaltedException
 *
 * Exception used to halt a route callback from executing in a dispatch loop
 */
class DispatchHaltedException extends RuntimeException implements RouterExceptionInterface
{
    /**
     * Constants
     */

    /**
     * Skip this current match/callback
     *
     * @var int
     */
    public const SKIP_THIS = 1;

    /**
     * Skip the next match/callback
     *
     * @var int
     */
    public const SKIP_NEXT = 2;

    /**
     * Skip the rest of the matches
     *
     * @var int
     */
    public const SKIP_REMAINING = 0;


    /**
     * Properties
     */

    /**
     * The number of next matches to skip on a "next" skip
     *
     * @var int
     */
    protected $number_of_skips = 1;


    /**
     * Methods
     */

    /**
     * Gets the number of matches to skip on a "next" skip
     *
     * @return int
     */
    public function getNumberOfSkips()
    {
        return $this->number_of_skips;
    }

    /**
     * Sets the number of matches to skip on a "next" skip
     *
     * @param int $number_of_skips
     * @return DispatchHaltedException
     */
    public function setNumberOfSkips($number_of_skips)
    {
        $this->number_of_skips = (int) $number_of_skips;

        return $this;
    }
}
