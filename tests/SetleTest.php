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
    public function testBrokerToken(): void
    {
        $api = new Setle('foo');
        $this->assertEquals('foo', $api->getBrokerToken());
    }

    public function testSetAccessToken(): void
    {
        $api = new Setle('foo');
        $api->setAccessToken('bar');
        $this->assertEquals('bar', $api->getAccessToken());
    }

    public function testSetAccessTokenArray(): void
    {
        $api = new Setle('foo');
        $api->setAccessToken([
            'access_token' => 'bar'
        ]);
        $this->assertEquals('bar', $api->getAccessToken());
    }

    public function testSetAccessTokenObject(): void
    {
        $api = new Setle('foo');
        $api->setAccessToken((object)[
            'access_token' => 'bar'
        ]);
        $this->assertEquals('bar', $api->getAccessToken());
    }

    public function testSetAccessTokenInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $api = new Setle('foo');
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
        $api = new Setle('foo');

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

    // WHISE

    public function testWhiseGetEstates(): void
    {
        self::$adapter->queueResponse('[{"foo": "bar"}]');
        self::$api->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $this->assertEquals('/v1/integrations/whise/estates', $endpoint);
        });

        $response = self::$api->whise()->getEstates();

        $this->assertEquals(1, $response->count());
    }

    public function testWhiseGetEstate(): void
    {
        self::$adapter->queueResponse('{"foo": "bar"}');
        self::$api->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $this->assertEquals('/v1/integrations/whise/estates/123', $endpoint);
        });

        $response = self::$api->whise()->getEstate('123');

        $this->assertEquals('bar', $response->foo);
    }

    // SKARABEE

    public function testSkarabeeGetEstates(): void
    {
        self::$adapter->queueResponse('[{"foo": "bar"}]');
        self::$api->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $this->assertEquals('/v1/integrations/skarabee/estates', $endpoint);
        });

        $response = self::$api->skarabee()->getEstates();

        $this->assertEquals(1, $response->count());
    }

    public function testSkarabeeGetEstate(): void
    {
        self::$adapter->queueResponse('{"foo": "bar"}');
        self::$api->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $this->assertEquals('/v1/integrations/skarabee/estates/123', $endpoint);
        });

        $response = self::$api->skarabee()->getEstate('123');

        $this->assertEquals('bar', $response->foo);
    }

    // SWEEPBRIGHT

    public function testSweepbrightGetEstates(): void
    {
        self::$adapter->queueResponse('[{"foo": "bar"}]');
        self::$api->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $this->assertEquals('/v1/integrations/sweepbright/estates', $endpoint);
        });

        $response = self::$api->sweepbright()->getEstates();

        $this->assertEquals(1, $response->count());
    }

    public function testSweepbrightGetEstate(): void
    {
        self::$adapter->queueResponse('{"foo": "bar"}');
        self::$api->debugResponses(function($response_body, $endpoint, $request_body) use (&$called) {
            $this->assertEquals('/v1/integrations/sweepbright/estates/123', $endpoint);
        });

        $response = self::$api->sweepbright()->getEstate('123');

        $this->assertEquals('bar', $response->foo);
    }
}
