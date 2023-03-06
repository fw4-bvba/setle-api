<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\ApiAdapter;

use Setle\Request\Request;
use Setle\Response\ResponseObject;

abstract class ApiAdapter implements ApiAdapterInterface
{
    /** @var callable|null */
    protected $debugCallable;

    /**
     * Send a request to the API and return the parsed response.
     *
     * @param Request $request
     *
     * @throws \Setle\Exception\AuthException if access token is missing or invalid
     * @throws \Setle\Exception\NotFoundException if requested resource is unavailable
     * @throws \Setle\Exception\ApiException if a server-side error occurred
     *
     * @return ResponseObject
     */
    public function request(Request $request): ResponseObject
    {
        $response_body = $this->requestBody($request);

        // Send response to debug callback
        if (isset($this->debugCallable)) {
            ($this->debugCallable)($response_body, $request->getEndpoint(), $request->getBody());
        }

        $response = json_decode($response_body, false);
        return new ResponseObject($response);
    }

    /**
     * Set a callback for debugging API requests and responses.
     *
     * @param callable|null $callable Callback that accepts up to three
     * arguments - respectively the response body, request endpoint and the
     * request body.
     *
     * @return Self
     */
    public function debugResponses(?callable $callable): self
    {
        $this->debugCallable = $callable;
        return $this;
    }
}
