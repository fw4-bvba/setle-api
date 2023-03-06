<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests\Response;

use PHPUnit\Framework\TestCase;
use DateTime;
use Setle\Response\ResponseObject;
use Setle\Exception\InvalidPropertyException;
use InvalidArgumentException;

class ResponseObjectTest extends TestCase
{
    public function testInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $object = new ResponseObject('test');
    }

    public function testArrayConstructor(): void
    {
        $object = new ResponseObject([1, 2, 3]);
        $data = $object->getData();

        $this->assertIsArray($data);
        $this->assertCount(3, $data);
    }

    public function testTraversableConstructor(): void
    {
        $input = (object)[
            'foo' => 10
        ];
        $object = new ResponseObject($input);
        $data = $object->getData();

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('foo', $data);
        $this->assertIsInt($data['foo']);
    }

    public function testMagicGet(): void
    {
        $input = (object)[
            'foo' => 10
        ];
        $object = new ResponseObject($input);

        $this->assertEquals(10, $object->foo);
    }

    public function testMagicSet(): void
    {
        $input = (object)[];
        $object = new ResponseObject($input);
        $object->foo = 10;

        $this->assertEquals(10, $object->foo);
    }

    public function testIsset(): void
    {
        $input = (object)[
            'foo' => 10
        ];
        $object = new ResponseObject($input);

        $this->assertTrue(isset($object->foo));
        $this->assertFalse(isset($object->bar));
    }

    public function testUnset(): void
    {
        $input = (object)[
            'foo' => 10
        ];
        $object = new ResponseObject($input);

        $this->assertTrue(isset($object->foo));
        unset($object->foo);
        $this->assertFalse(isset($object->foo));
    }

    public function testInvalidProperty(): void
    {
        $this->expectException(InvalidPropertyException::class);

        $input = (object)[];
        $object = new ResponseObject($input);

        $invalid = $object->invalid;
    }

    public function testParsing(): void
    {
        $input = (object)[
            'object' => (object)[
                'foo' => 10
            ],
            'array' => [1, 2, 3],
            'date' => '2020-01-01T12:00:00Z',
            'null' => null,
        ];
        $object = new ResponseObject($input);

        $this->assertInstanceOf(ResponseObject::class, $object->object);
        $this->assertEquals(10, $object->object->foo);
        $this->assertIsArray($object->array);
        $this->assertCount(3, $object->array);
        $this->assertTrue($object->date instanceof DateTime);
        $this->assertNull($object->null);
    }

    public function testJsonSerialize(): void
    {
        $input = (object)[
            'foo' => 10
        ];
        $object = new ResponseObject($input);
        $data = json_decode(json_encode($object) ?: '{}', false);

        $this->assertEquals(10, $data->foo);
    }
}
