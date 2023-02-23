<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\ApiAdapter;

use Setle\Request\Request;
use Setle\Exception\AuthException;
use Setle\Exception\NotFoundException;
use Setle\Exception\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use PackageVersions\Versions;

/**
 * @codeCoverageIgnore
 */
final class HttpApiAdapter extends ApiAdapter
{
    private const BASE_URI = 'https://public-api.setle.app/v1/';

    /** @var Client */
    private $client;

    /**
     * @param array<mixed> $http_client_options
     */
    public function __construct(array $http_client_options = [])
    {
        $http_client_options['base_uri'] = self::BASE_URI;
        if (!isset($http_client_options['headers']) || !is_array($http_client_options['headers'])) {
            $http_client_options['headers'] = [];
        }
        if (empty($http_client_options['headers']['User-Agent'])) {
            $version = Versions::getVersion('fw4/setle-api');
            $http_client_options['headers']['User-Agent'] = 'fw4-setle-api/' . $version;
        }
        $http_client_options['headers']['Accept'] = 'application/json';
        $http_client_options['headers']['Content-Type'] = 'application/json';

        $this->client = new Client(array_merge([
            'timeout' => 30.0,
            'http_errors' => false,
        ], $http_client_options));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Setle\Exception\AuthException if access token is missing or invalid
     * @throws \Setle\Exception\NotFoundException if requested resource is unavailable
     * @throws \Setle\Exception\ApiException if a server-side error occurred
     *
     * @return string
     */
    public function requestBody(Request $request): string
    {
        $parameters = $request->getParameters();
        $headers = $request->getHeaders();
        $body = $request->getBody();

        $options = [];
        if (count($parameters)) {
            $options['query'] = $parameters;
        }
        if (count($headers)) {
            $options['headers'] = $headers;
        }
        if ($body) {
            $options['body'] = $body;
        }

        $guzzle_request = new GuzzleRequest($request->getMethod(), $request->getEndpoint(), $headers);
        $response = $this->client->send($guzzle_request, $options);
        $body = $response->getBody()->getContents();

        // Handle errors
        if ($response->getStatusCode() >= 400) {
            $response_data = @json_decode($body, false);
            $message = $response_data->message ?? $response->getReasonPhrase();
            if ($response->getStatusCode() === 401) {
                throw new AuthException($message, 401);
            } elseif ($response->getStatusCode() === 404) {
                throw new NotFoundException($message, 404);
            } else {
                throw new ApiException($message, $response->getStatusCode());
            }
        }

        return $body;
    }
}
