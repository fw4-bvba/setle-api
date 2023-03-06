<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Request;

use Setle\Exception\FailedJsonEncodingException;
use DateTime;

class Request
{
    /** @var string */
    protected $method;

    /** @var string */
    protected $endpoint;

    /** @var array<string> */
    protected $parameters;

    /** @var array<string> */
    protected $headers;

    /** @var array<mixed>|string|null */
    protected $body;

    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed $body
     * @param array<mixed> $parameters
     * @param array<mixed> $headers
     */
    public function __construct(
        string $method,
        string $endpoint,
        $body = null,
        array $parameters = [],
        array $headers = []
    ) {
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setParameters($parameters);
        $this->setHeaders($headers);
        if (isset($body)) {
            $this->setBody($body);
        }
    }

    /**
     * Set the access token (bearer token) to use for authentication.
     *
     * @param string $access_token
     *
     * @return self
     */
    public function setAccessToken(string $access_token): Request
    {
        $this->headers['Authorization'] = 'Bearer ' . $access_token;
        return $this;
    }

    /**
     * Set the HTTP method to use for this request.
     *
     * @param string $method HTTP method like GET, POST, PATCH or DELETE
     *
     * @return self
     */
    public function setMethod(string $method): Request
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get the HTTP method to use for this request.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set the endpoint/URI path to query.
     *
     * @param string $endpoint
     *
     * @return self
     */
    public function setEndpoint(string $endpoint): Request
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Get the endpoint/URI path to query.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Set the HTTP query string parameters.
     *
     * @param array<string> $parameters Associative array of parameter names and values
     *
     * @return self
     */
    public function setParameters(array $parameters): Request
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Get the HTTP query string parameters.
     *
     * @return array<string> Unencoded associative array of parameter names and values
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Set additional HTTP headers.
     *
     * @param array<string> $headers Associative array of header names and values
     *
     * @return self
     */
    public function setHeaders(array $headers): Request
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get additional HTTP headers.
     *
     * @return array<string> Associative array of header names and values
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set the HTTP body to send.
     *
     * @param array<mixed>|string $body Raw string or associative array to send as JSON
     *
     * @return self
     */
    public function setBody($body): Request
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get the HTTP body to send.
     *
     * @return string|null
     */
    public function getBody(): ?string
    {
        if (is_null($this->body) || is_string($this->body)) {
            return $this->body;
        } else {
            $json = json_encode($this->encode($this->body)) ?: '';
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new FailedJsonEncodingException('Json Encoding for "' . gettype($this->body) . '" failed.');
            }
            return $json;
        }
    }

    /**
     * Recursively encode a value into a format understood by the Setle API.
     *
     * @param mixed $encodable
     *
     * @return string
     */
    protected function encode($encodable)
    {
        if (is_array($encodable)) {
            foreach ($encodable as $key => $value) {
                $encodable[$key] = $this->encode($value);
            }
        } elseif ($encodable instanceof DateTime) {
            return $encodable->format('c');
        }
        return $encodable;
    }
}
