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

final class Setle
{
    /** @var ApiAdapterInterface */
    private $apiAdapter;

    /** @var string */
    private $brokerToken;

    /** @var string */
    private $accessToken;

    /** @var array */
    private $integrationEndpoints = [];

    public function __construct(string $broker_token)
    {
        $this->setBrokerToken($broker_token);
    }

    // ENDPOINTS

    public function whise(): IntegrationEndpoint
    {
        return $this->getIntegrationEndpoint('whise');
    }

    public function skarabee(): IntegrationEndpoint
    {
        return $this->getIntegrationEndpoint('skarabee');
    }

    public function sweepbright(): IntegrationEndpoint
    {
        return $this->getIntegrationEndpoint('sweepbright');
    }

    protected function getIntegrationEndpoint(string $integration_name): IntegrationEndpoint
    {
        if (!isset($this->integrationEndpoints[$integration_name])) {
            $this->integrationEndpoints[$integration_name] = new IntegrationEndpoint($this, $integration_name);
        }
        return $this->integrationEndpoints[$integration_name];
    }

    // BROKER TOKEN

    /**
     * Set broker token to use for authentication.
     *
     * @param string $broker_token
     *
     * @return self
     */
    public function setBrokerToken(string $broker_token): self
    {
        $this->brokerToken = $broker_token;
        $this->accessToken = null;
        return $this;
    }

    /**
     * Get the current broker token.
     *
     * @return string
     */
    public function getBrokerToken(): string
    {
        return $this->brokerToken;
    }

    // ACCESS TOKEN

    /**
     * Use a previously requested access token.
     *
     * @param mixed $access_token
     */
    public function setAccessToken($access_token): self
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
        return $this;
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
        $request = new Request('POST', 'agency/login', [
            'token' => $this->getBrokerToken(),
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

    public function setApiAdapter(ApiAdapterInterface $adapter): self
    {
        $this->apiAdapter = $adapter;
        return $this;
    }

    public function getApiAdapter(): ApiAdapterInterface
    {
        if (!isset($this->apiAdapter)) {
            $this->setApiAdapter(new HttpApiAdapter());
        }
        return $this->apiAdapter;
    }

    // DEBUGGING

    /**
     * Set a callback for debugging API requests and responses.
     *
     * @param callable|null $callable Callback that accepts up to three
     * arguments - respectively the response body, request endpoint, and the
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
