<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Database;

class Searcher extends \Labi\Database\Searcher implements \Rumi\Orm\SearcherInterface
{
    private $recordClass;
    private $definition;
    private $target;
    private $adapter;

    public function __construct($adapter)
    {
        parent::__construct($adapter);

        $this->adapter = $adapter;
    }

    // + SearcherInterface
    public function id($id)
    {
        if (!is_array($id)) {
            throw new \Exception("ID for database searcher should be array.");
        }

        foreach ($id as $column => $value) {
            $this->eq($column, $value);
        }

        return $this;
    }

    public function definition(\Rumi\Orm\Definition $definition = null)
    {
        if (is_null($definition)) {
            return $this->definition;
        }

        $this->definition = $definition;

        return $this;
    }

    public function recordClass($recordClass = null)
    {
        if (is_null($recordClass)) {
            return $this->recordClass;
        }

        $this->recordClass = $recordClass;

        return $this;
    }

    public function target($target = null)
    {
        if (is_null($target)) {
            return $this->target;
        }

        $this->target = $target;

        // wywoluje metode ustawiajaca from
        parent::from($target);

        return $this;
    }

    public function from($table, $alias = null)
    {
        // wywoluje metode ustawiajaca from
        parent::from($table, $alias);

        $this->target = $table;

        return $this;
    }

    public function first($params = array())
    {
        return $this->all($params)->first();
    }

    public function all($params = array())
    {
        if (is_null($this->recordClass)) {
            throw new \Exception("No recordClass defined for searcher.");
        }

        if (is_null($this->definition)) {
            throw new \Exception("No definition defined for searcher.");
        }

        // pobieram wiersze
        $rows = $this->fetch($params);

        // tworze searcher dla kolekcji
        $searcher = clone($this);
        foreach ($params as $name => $value) {
            $searcher->param($name, $value);
        }

        return new \Rumi\Orm\Collection(
            $this->adapter,
            $this->target,
            $this->definition,
            $this->recordClass,
            $searcher,
            $rows
        );
    }

    public function fetch($params = array())
    {
        return parent::search($params);
    }
    // - SearcherInterface
}
