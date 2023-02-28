<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle\Response;

use InvalidArgumentException;
use Setle\Exception\InvalidPropertyException;

class ResponseObject implements \JsonSerializable
{
    /** @var array<mixed> */
    protected $data = [];

    /**
     * @param mixed $data
     *
     * @return self
     */
    public function __construct($data)
    {
        if (!is_iterable($data) && !is_object($data)) {
            throw new InvalidArgumentException('ResponseObject does not accept data of type "' . gettype($data) . '"');
        }
        foreach ($data as $property => &$value) {
            if (is_array($data)) {
                $this->data[] = $this->parseValue($value);
            } else {
                $this->data[$property] = $this->parseValue($value);
            }
        }
    }

    /**
     * Recursively parse Setle API data.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function parseValue($value)
    {
        if (is_object($value)) {
            return new self($value);
        } elseif (is_array($value)) {
            $result = [];
            foreach ($value as &$subvalue) {
                $result[] = $this->parseValue($subvalue);
            }
            return $result;
        } elseif (
            is_string($value) &&
            preg_match('/^(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|' .
            '(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|' .
            '[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|' .
            '2[0-3]):[0-5]\d:[0-5]\d(?:\.\d{1,9})?(?:Z|[+-][01]\d:[0-5]\d)?$/', $value)
        ) {
            return new \DateTime($value);
        } else {
            return $value;
        }
    }

    /**
     * Get all properties of this object.
     *
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get value of specific property of this object.
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        $this->validatePropertyName($property);
        return $this->data[$property] ?? null;
    }

    /**
     * Set value of specific property of this object.
     *
     * @param string $property
     * @param mixed $value
     *
     */
    public function __set(string $property, $value): void
    {
        $this->data[$property] = $value;
    }

    public function __isset(string $property): bool
    {
        return isset($this->data[$property]);
    }

    public function __unset(string $property)
    {
        $this->validatePropertyName($property);
        unset($this->data[$property]);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array<mixed>
     */
    public function __debugInfo()
    {
        return $this->getData();
    }

    /**
     * Check if this object contains a specific property.
     *
     * @param string $property
     *
     * @throws InvalidPropertyException if the property does not exist
     *
     * @return string
     */
    protected function validatePropertyName(string $property): string
    {
        if (!array_key_exists($property, $this->data)) {
            throw new InvalidPropertyException($property . ' is not a valid property of ' . static::class);
        }
        return $property;
    }

    /* JsonSerializable implementation */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getData();
    }
}
