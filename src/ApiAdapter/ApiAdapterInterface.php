<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\ApiAdapter;

use Setle\Request\Request;

interface ApiAdapterInterface
{
    /**
     * Send a request to the API and return the raw response body.
     *
     * @param Request $request
     *
     * @return string
     */
    public function requestBody(Request $request): string;
}
