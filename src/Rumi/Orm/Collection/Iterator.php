<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Orm\Collection;

class Iterator implements \SeekableIterator, \Countable
{
    private $position = 0;
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    // + Countable
    public function count()
    {
        return $this->collection->count();
    }
    // - Countable

    // + SeekableIterator
    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->collection->get($this->position);
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return ($this->position < $this->collection->count());
    }

    public function seek($position)
    {
        if($position < 0 || $position >= $this->collection->count()){
            throw new \OutOfBoundsException(printf("Invalid seek position %s", $position));
        }

        $this->position = $position;
    }
    // - SeekableIterator
}
