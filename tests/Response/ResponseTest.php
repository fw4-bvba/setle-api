<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Tests\Response;

use PHPUnit\Framework\TestCase;
use Setle\Response\Response;
use Setle\Response\ResponseObject;

class ResponseTest extends TestCase
{
    public function testConstructor(): void
    {
        $input = (object)[
            'foo' => 10
        ];
        $response_data = new ResponseObject($input);
        $response = new Response($response_data);
        $data = $response->getData();

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('foo', $data);
        $this->assertIsInt($data['foo']);
    }
}
