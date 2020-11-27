<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests\ApiAdapter;

use Setle\Tests\ApiTestCase;
use Setle\Request\Request;
use Setle\Exception\InvalidRequestException;

class ApiAdapterTest extends ApiTestCase
{
    public function testDebugCallable(): void
    {
        $called = false;
        $request = new Request('GET', 'endpoint', 'body');

        self::$adapter->queueResponse('{}');
        self::$adapter->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $called = true;

            $this->assertEquals('{}', $response_body);
            $this->assertEquals('endpoint', $endpoint);
            $this->assertEquals('body', $request_body);
        });

        $response = self::$adapter->request($request);

        $this->assertTrue($called);
    }
}
