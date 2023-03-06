<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests\Response;

use PHPUnit\Framework\TestCase;
use Setle\Response\ResponseObject;
use Setle\Response\CollectionResponse;
use PHPUnit\Framework\Error\Notice;

class CollectionResponseTest extends TestCase
{
    /** @var ResponseObject */
    protected static $responseData;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$responseData = new ResponseObject([1, 2, 3]);
    }

    public function testGet(): void
    {
        $object = new CollectionResponse(self::$responseData);

        $this->assertEquals(2, $object->get(1));
    }

    public function testCount(): void
    {
        $object = new CollectionResponse(self::$responseData);

        $this->assertCount(3, $object);
    }

    public function testIsset(): void
    {
        $object = new CollectionResponse(self::$responseData);

        $this->assertTrue(isset($object[2]));
        $this->assertFalse(isset($object[3]));

        // @phpstan-ignore-next-line
        $this->assertFalse(isset($object['string']));
    }

    public function testOffsetGet(): void
    {
        $object = new CollectionResponse(self::$responseData);

        $this->assertEquals(3, $object[2]);
    }

    public function testIterator(): void
    {
        $object = new CollectionResponse(self::$responseData);

        foreach ($object as $index => $value) {
            $this->assertEquals($value, $index + 1);
        }
    }
}
