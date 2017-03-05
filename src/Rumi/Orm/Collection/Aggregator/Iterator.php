<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Orm\Collection\Aggregator;

class Iterator implements \SeekableIterator, \Countable
{
    private $position = 0;
    private $groups = array();
    private $aggregator;

    public function __construct($aggregator)
    {
        $this->aggregator = $aggregator;
        $this->groups = $aggregator->groups();
    }

    // + Countable
    public function count()
    {
        return $this->aggregator->count();
    }
    // - Countable

    // + SeekableIterator
    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->aggregator->get($this->groups[$this->position]);
    }

    public function key()
    {
        return $this->groups[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->groups[$this->position]);
    }

    public function seek($position)
    {
        $index = null;

        foreach ($this->groups as $key => $group) {
            if (md5(var_export($groups, true)) === md5(var_export($position, true))) {
                $index = $key;
            }
        }

        if(is_null($index)){
            throw new \OutOfBoundsException(sprintf("Invalid seek position %s", $position));
        }

        $this->position = $index;
    }
    // - SeekableIterator
}
