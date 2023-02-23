<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle;

use Setle\Request\Request;
use Setle\Response\Response;
use Setle\Response\ResponseObject;
use Setle\ApiAdapter\HttpApiAdapter;
use Setle\ApiAdapter\ApiAdapterInterface;
use Setle\Exception\AuthException;
use InvalidArgumentException;
use Setle\ApiAdapter\ApiAdapter;
use Setle\Response\CollectionResponse;

final class Setle
{
    /** @var ApiAdapter|null */
    private $apiAdapter;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string|null */
    private $accessToken;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->setCredentials($clientId, $clientSecret);
    }

    // CREDENTIALS

    /**
     * Set Client ID and Client Secret to use for authentication.
     *
     * @param string $client_id, $client_secret
     *
     * @return self
     */
    public function setCredentials(string $client_id, string $client_secret): self
    {
        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->accessToken = null;
        return $this;
    }

    /**
     * Get the current client id.
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Get the current client secret.
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    // ACCESS TOKEN

    /**
     * Use a previously requested access token.
     *
     * @param mixed $access_token
     */
    public function setAccessToken($access_token): string
    {
        if (is_array($access_token) && isset($access_token['access_token'])) {
            $access_token = strval($access_token['access_token']);
        } elseif (is_object($access_token) && isset($access_token->access_token)) {
            $access_token = strval($access_token->access_token);
        }
        if (!is_string($access_token)) {
            throw new InvalidArgumentException('Invalid access token provided');
        }
        $this->accessToken = $access_token;
        return $access_token;
    }

    /**
     * Get the current access token.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        if (is_null($this->accessToken)) {
            $this->setAccessToken($this->requestAccessToken());
        }
        return $this->accessToken;
    }

    /**
     * Request a new access token using the broker token.
     *
     * @return Response
     */
    public function requestAccessToken(): Response
    {
        $request = new Request('POST', 'oauth/token', null, [], [
            'x-setle-client-id' => $this->getClientId(),
            'x-setle-client-secret' => $this->getClientSecret(),
        ]);
        return new Response($this->getApiAdapter()->request($request));
    }

    /**
     * Send a request to the API and return the parsed response. Will reattempt
     * the request if it fails due to an expired access token.
     *
     * @param Request $request
     *
     * @throws Exception\AuthException if access token is missing or invalid
     * @throws Exception\NotFoundException if requested resource is unavailable
     * @throws Exception\ApiException if a server-side error occurred
     *
     * @return ResponseObject
     */
    public function request(Request $request): ResponseObject
    {
        $request->setAccessToken($this->getAccessToken());
        try {
            return $this->getApiAdapter()->request($request);
        } catch (AuthException $exception) {
            $this->setAccessToken($this->requestAccessToken());
            $request->setAccessToken($this->getAccessToken());
            return $this->getApiAdapter()->request($request);
        }
    }

    // API ADAPTER

    public function setApiAdapter(ApiAdapter $adapter): self
    {
        $this->apiAdapter = $adapter;
        return $this;
    }

    public function getApiAdapter(): ApiAdapter
    {
        if (!isset($this->apiAdapter)) {
            $this->setApiAdapter(new HttpApiAdapter());
        }
        return $this->apiAdapter;
    }

    // GET DATA

    /**
     * Get all estates.
     *
     * @throws Exception\AuthException if access token is missing or invalid
     * @throws Exception\ApiException if a server-side error occurred
     *
     * @return CollectionResponse
    */
    public function getEstates(): CollectionResponse
    {
        $request = new Request('GET', 'estate/list');
        return new CollectionResponse($this->request($request));
    }

    // DEBUGGING

    /**
     * Set a callback for debugging API requests and responses.
     *
     * @param callable|null $callable Callback that accepts up to three
     * arguments - respectively the response body, and the
     * request body.
     *
     * @return self
     */
    public function debugResponses(?callable $callable): self
    {
        $this->getApiAdapter()->debugResponses($callable);
        return $this;
    }
}
