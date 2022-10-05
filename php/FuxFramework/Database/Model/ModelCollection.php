<?php

namespace Fux\Database\Model;


use function Clue\StreamFilter\fun;

class ModelCollection implements \Countable, \IteratorAggregate, \JsonSerializable, \ArrayAccess
{

    /** @property Model[] $data */
    private $data = [];

    /**
     * Create a collection of model instances
     *
     * @param Model[] $data
     *
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Return a collection of model instances
     *
     * @return Model[]
     */
    public function getData()
    {
        return $this->data;
    }

    public function toArray(){
        return $this->getData();
    }


    /**
     * Append another collection to the current one
     *
     * @param ModelCollection | Model[] $collection
     */
    public function concat($collection)
    {
        if ($collection instanceof ModelCollection) {
            $this->data = array_merge($this->data, $collection->getData());
        }else{
            $this->data = array_merge($this->data, $collection);
        }
    }


    /**
     * Tests whether all elements in the collection pass the test implemented by the provided
     * function.
     *
     * @param callable $test
     * <p>
     * The test function is called with the following arguments:
     * - element: The current model instance being processed in the array.
     * - index: The index of the current element being processed in the collection
     * - collection: The collection instance
     * </p>
     *
     * @return bool
     */
    public function every($test)
    {
        foreach ($this->data as $i => $m) {
            if (!$test($m, $i, $this)) return false;
        }
        return true;
    }


    /**
     * Set some columns data of all the instances in the collection to a static value from a starting index.
     *
     * @param array $data
     * @param int $start Start index (inclusive), default 0
     * @param int $end End index (exclusive), default count($this)
     *
     * @return self
     */
    public function fill($data, $start = 0, $end = null)
    {
        if ($end === null) $end = count($this);
        for ($i = $start; $i < $end; $i++) {
            $this->data[$i]->overwrite($data);
        }
        return $this;
    }


    /**
     * Return the first instance in the collection that pass the test implemented by the provided
     * function.
     *
     * @param callable $test
     * <p>
     * The test function is called with the following arguments:
     * - element: The current model instance being processed in the array.
     * - index: The index of the current element being processed in the collection
     * - collection: The collection instance
     * </p>
     *
     * @return Model | null
     */
    public function find($test)
    {
        foreach ($this->data as $i => $m) {
            if ($test($m, $i, $this)) return $m;
        }
        return null;
    }


    /**
     * Return whether exists an instance in the collection that pass the test implemented by the provided
     * function.
     *
     * @param callable $test
     * <p>
     * The test function is called with the following arguments:
     * - element: The current model instance being processed in the array.
     * - index: The index of the current element being processed in the collection
     * - collection: The collection instance
     * </p>
     *
     * @return bool
     */
    public function some($test)
    {
        return !!$this->find($test);
    }


    /**
     * Reverse the order of the instances in the collection
     *
     * @return self
     */
    public function reverse()
    {
        $this->data = array_reverse($this->data);
        return $this;
    }


    /**
     * Return a new collection composed by all instances  that pass the test implemented by the provided function
     *
     * @param callable $test
     * <p>
     * The test function is called with the following arguments:
     * - element: The current model instance being processed in the array.
     * - index: The index of the current element being processed in the collection
     * - collection: The collection instance
     * </p>
     *
     * @return ModelCollection
     */
    public function filter($test)
    {
        $items = array_filter($this->data, function ($m, $i) use ($test) {
            return $test($m, $i, $this);
        });
        return new ModelCollection($items);
    }


    /**
     * Return a new collection composed by all instances with primary key not matched with the ones passed. If partial
     * primary key are present either in except instances or collection instances the instance will be labeled as "correct"
     * and will belong to the output collection.
     *
     * @param Model[] $instances
     *
     * @return ModelCollection
     */
    public function except($instances)
    {
        $items = array_filter($this->data, function ($m) use ($instances) {
            foreach ($instances as $e) {
                if ($m->is($e)) return false;
            }
            return true;
        });
        return new ModelCollection($items);
    }




    /**
     * Return an array where each i-th element is the value of the wanted column of the i-th element in the collection
     *
     * @param string $columnName
     *
     * @return array
     */
    public function column($columnName)
    {
        return array_map(fn($row) => $row[$columnName],$this->getData());
    }

    public function isEmpty()
    {
        return count($this->data) == 0;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}