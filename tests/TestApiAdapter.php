<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests;

use Setle\ApiAdapter\ApiAdapter;
use Setle\Request\Request;
use Exception;

final class TestApiAdapter extends ApiAdapter
{
    /** @var array<string|Exception> */
    protected $responseQueue = [];

    public function clearQueue(): void
    {
        $this->responseQueue = [];
    }

    public function queueResponse(string $body): void
    {
        $this->responseQueue[] = $body;
    }

    public function queueException(Exception $exception): void
    {
        $this->responseQueue[] = $exception;
    }

    /**
     * {@inheritdoc}
    */
    public function requestBody(Request $request): string
    {
        if (count($this->responseQueue) === 0) {
            return '{}';
        }

        $response = $this->responseQueue[0];
        array_shift($this->responseQueue);

        if ($response instanceof Exception) {
            throw $response;
        }

        return $response;
    }
}
