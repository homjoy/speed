<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Frame\Speed\Lib\Ical;

use Frame\Speed\Lib\Ical\Property;

class PropertyBag implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $elements = array();

    /**
     * Creates a new Property with $name, $value and $params
     *
     * @param $name
     * @param $value
     * @param array $params
     */
    public function set($name, $value, $params = array())
    {
        $property         = new Property($name, $value, $params);
        $this->elements[] = $property;
    }

    /**
     * @param string $key
     * @return null|Property
     */
    public function get($name)
    {
        // Searching Property in elements-array
        foreach ($this->elements as $property) {
            if ($property->getName() == $name) {
                return $property;
            }
        }

        return null;
    }

    /**
     * Adds a Property. If Property already exists an Exception will be thrown.
     *
     * @param Property $property
     * @throws \Exception
     */
    public function add(Property $property)
    {
        // Property already exists?
        if (null !== $this->get($property->getName())) {
            throw new \Exception("Property with name '{$property->getName()}' already exists");
        }

        $this->elements[] = $property;
    }

    public function getIterator()
    {
        return new \ArrayObject($this->elements);
    }
}
