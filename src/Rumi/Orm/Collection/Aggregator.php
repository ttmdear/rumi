<?php

namespace Rumi\Orm\Collection;

class Aggregator implements \IteratorAggregate
{
    private $collections = array();

    public function get($index)
    {
        $key = md5(var_export($index, true));

        if (!array_key_exists($key, $this->collections)) {
            throw new \Exception(sprintf("There is no %s index at aggregator.", implode(',', $index)));
        }

        return $this->collections[$key]['collection'];
    }

    public function all()
    {
        $collections = array();

        foreach ($this->collections as $collection) {
            $collections[] = $collection['collection'];
        }

        return $collections;
    }

    public function groups()
    {
        $groups = array();

        foreach ($this->collections as $collection) {
            $groups[] = $collection['index'];
        }

        return $groups;
    }

    public function add($index, $collection)
    {
        $key = md5(var_export($index, true));

        $this->collections[$key] = array(
            'collection' => $collection,
            'index' => $index,
        );

        return $this;
    }

    // + IteratorAggregate
    public function getIterator()
    {
        return new \Rumi\Orm\Collection\Aggregator\Iterator($this);
    }
    // - IteratorAggregate

    // + Countable
    public function count()
    {
        return count($this->collections);
    }
    // + Countable
}
