<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Response;

/**
 * @implements \IteratorAggregate<int, string>
 * @implements \ArrayAccess<int, string>
*/
class CollectionResponse implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /** @var array<mixed> */
    protected $data;

    public function __construct(ResponseObject $data)
    {
        $this->data = (array_key_exists('estates', $data->getData()) ?
        array_values($data->getData()['estates']) : $data->getData());
    }

    /**
     * Get the item at a specific index.
     *
     * @return mixed
     */
    public function get(int $position)
    {
        return $this->data[$position];
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array<mixed>
     *
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /* Countable implementation */

    public function count(): int
    {
        return count($this->data);
    }

    /* IteratorAggregate implementation */

    public function getIterator(): CollectionResponseIterator
    {
        return new CollectionResponseIterator($this);
    }

    /* ArrayAccess implementation */

    public function offsetExists($offset): bool
    {
        if (!is_int($offset)) {
            return false;
        }
        return $offset >= 0 && $offset < $this->count();
    }

    /* JsonSerializable implementation */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            trigger_error('Undefined offset: ' . $offset);
        }
        return $this->get($offset);
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetSet($offset, $value): void
    {
        throw new \Exception('offsetSet not implemented on CollectionResponse');
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetUnset($offset): void
    {
        throw new \Exception('offsetUnset not implemented on CollectionResponse');
    }
}
