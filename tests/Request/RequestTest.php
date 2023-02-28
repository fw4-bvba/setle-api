<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests\Request;

use PHPUnit\Framework\TestCase;
use Setle\Request\Request;
use DateTime;

class RequestTest extends TestCase
{
    public function testConstructor(): void
    {
        $method = 'GET';
        $endpoint = 'endpoint';
        $body = 'body';
        $params = ['foo' => 'bar'];
        $headers = ['baz' => 'quux'];

        $request = new Request($method, $endpoint, $body, $params, $headers);

        $this->assertEquals($request->getMethod(), $method);
        $this->assertEquals($request->getEndpoint(), $endpoint);
        $this->assertEquals($request->getBody(), $body);
        $this->assertEquals($request->getParameters(), $params);
        $this->assertEquals($request->getHeaders(), $headers);
    }

    public function testEncode(): void
    {
        $request = new Request('GET', '', [
            'array' => ['foo' => 'bar'],
            'date' => new DateTime('2020-01-01 12:00:00'),
        ]);

        $body = json_decode($request->getBody() ?? '{}', true);

        $this->assertEquals($body['array'], ['foo' => 'bar']);
        $this->assertEquals(substr($body['date'], 0, 19), '2020-01-01T12:00:00');
    }

    public function testSetAccessToken(): void
    {
        $request = new Request('GET', '');
        $request->setAccessToken('foo');

        $headers = $request->getHeaders();

        $this->assertEquals($headers['Authorization'] ?? '', 'Bearer foo');
    }
}
