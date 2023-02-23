<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests;

use PHPUnit\Framework\TestCase;
use Setle\Setle;
use Exception;

abstract class ApiTestCase extends TestCase
{
    protected static $adapter;
    protected static $api;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$adapter = new TestApiAdapter();
        self::$api = new Setle('', '');
        self::$api->setApiAdapter(self::$adapter);
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::$adapter->clearQueue();
        self::$adapter->debugResponses(null);
    }

    public function queueResponse($body): void
    {
        if (!is_string($body)) {
            $body = json_encode($body);
        }
        self::$adapter->queueResponse($body);
    }

    public function queueException(Exception $exception): void
    {
        self::$adapter->queueException($exception);
    }
}
