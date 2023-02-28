<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests;

use Setle\Setle;
use Setle\Request\Request;
use Setle\ApiAdapter\HttpApiAdapter;
use Setle\Exception\AuthException;
use InvalidArgumentException;

class SetleTest extends ApiTestCase
{
    public function testClientId(): void
    {
        $api = new Setle('foo', 'bar');
        $this->assertEquals('foo', $api->getClientId());
    }

    public function testClientSecret(): void
    {
        $api = new Setle('foo', 'bar');
        $this->assertEquals('bar', $api->getClientSecret());
    }

    public function testSetAccessToken(): void
    {
        $api = new Setle('foo', 'bar');
        $api->setAccessToken('bar');
        $this->assertEquals('bar', $api->getAccessToken());
    }

    public function testSetAccessTokenArray(): void
    {
        $api = new Setle('foo', 'bar');
        $api->setAccessToken([
            'access_token' => 'bar'
        ]);
        $this->assertEquals('bar', $api->getAccessToken());
    }

    public function testSetAccessTokenObject(): void
    {
        $api = new Setle('foo', 'bar');
        $api->setAccessToken((object)[
            'access_token' => 'bar'
        ]);
        $this->assertEquals('bar', $api->getAccessToken());
    }

    public function testSetAccessTokenInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $api = new Setle('foo', 'bar');
        $api->setAccessToken(1);
    }

    public function testRequestAccessToken(): void
    {
        $this->queueResponse('{"access_token": "bar"}');
        $response = self::$api->requestAccessToken();

        $this->assertEquals('bar', $response->access_token);
    }

    public function testDefaultApiAdapter(): void
    {
        $api = new Setle('foo', 'bar');

        $this->assertInstanceOf(HttpApiAdapter::class, $api->getApiAdapter());
    }

    public function testAutomaticAccessToken(): void
    {
        $unique_token = uniqid();
        $this->queueResponse('{"access_token": "' . $unique_token . '"}');
        $token = self::$api->getAccessToken();

        $this->assertEquals($unique_token, $token);
    }

    public function testAccessTokenExpirationRetry(): void
    {
        $this->queueException(new AuthException(''));
        $unique_token = uniqid();
        $this->queueResponse('{"access_token": "' . $unique_token . '"}');
        $this->queueResponse('{"foo": "bar"}');

        $request = new Request('GET', 'endpoint', 'body');
        $response = self::$api->request($request);

        $this->assertEquals('bar', $response->foo);
        $this->assertEquals($unique_token, self::$api->getAccessToken());
    }

    // Estates

    public function testGetEstates(): void
    {
        $called = false;

        self::$adapter->queueResponse('[{"foo": "bar"}]');
        self::$api->debugResponses(function ($response_body, $endpoint, $request_body) use (&$called) {
            $called = true;

            $this->assertEquals('estate/list', $endpoint);
            $this->assertEquals('[{"foo": "bar"}]', $response_body);
            $this->assertEquals(null, $request_body);
        });

        $response = self::$api->getEstates();

        $this->assertTrue($called);
        $this->assertEquals(1, $response->count());
    }
}
