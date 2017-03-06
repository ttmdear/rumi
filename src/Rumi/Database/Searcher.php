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

/**
 * Klasa obslugujaca obiekt Searchera dla bazy danych. Glownie korzysta z
 * obiektu Searchera z biblioteki Labi.
 */
class Searcher implements \Rumi\Orm\SearcherInterface
{
    private $recordClass;
    private $definition;
    private $target;
    private $adapter;
    private $searcher;

    public function __construct(\Rumi\Adapters\AdapterInterface $adapter, $searcher)
    {
        $this->adapter = $adapter;
        $this->searcher = $searcher;
    }

    // + magic
    public function __clone()
    {
        $this->searcher = clone($this->searcher);
    }
    // - magic

    // + \Rumi\Orm\SearcherInterface
    public function id($id)
    {
        if (!is_array($id)) {
            throw new \Exception("ID for database searcher should be array.");
        }

        foreach ($id as $column => $value) {
            $this->searcher->column($column, false)->eq($value);
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
        $this->searcher->from($target);

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
        $rows = $this->searcher->search($params);

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

    public function fetch($params = array(), $options = array())
    {
        return $this->searcher->search();
    }
    // - \Rumi\Orm\SearcherInterface

    // + \Labi\Database\Searcher
    public function params($proccess = false)
    {
        $this->searcher->params($proccess);
        return $this;
    }

    public function param($name, $value, $proccess = false)
    {
        $this->searcher->param($name, $value, $proccess);
        return $this;
    }

    public function from($table, $alias = null)
    {
        $this->searcher->from($table, $alias);
        return $this;
    }

    public function alias()
    {
        return $this->searcher->alias();
    }

    public function table()
    {
        return $this->searcher->table();
    }

    public function column($cname, $show = true)
    {
        return new \Rumi\Database\Column($this, $this->searcher->column($cname, $show));
    }

    public function columns($columns)
    {
        $this->searcher->columns($columns);
        return $this;
    }

    public function innerJoin($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->searcher->innerJoin($table, $alias));
    }

    public function outerJoin($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->searcher->outerJoin($table, $alias));
    }

    public function leftJoin($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->searcher->leftJoin($table, $alias));
    }

    public function join($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->searcher->join($table, $alias));
    }

    public function defaultRule($defaultRule)
    {
        $this->searcher->defaultRule($defaultRule);
        return $this;
    }

    public function rule($params, $rule = null)
    {
        $this->searcher->rule($params, $rule);
        return $this;
    }

    public function proccess($params = array())
    {
        $this->searcher->proccess($params);
        return $this;
    }

    public function brackets($function, $scope = null)
    {
        if (is_null($scope)) {
            $scope = $this;
        }

        $this->searcher->brackets($function, $scope);

        return $this;
    }

    public function andOperator()
    {
        $this->searcher->andOperator();
        return $this;
    }

    public function orOperator()
    {
        $this->searcher->orOperator();
        return $this;
    }

    public function in($column, $value)
    {
        $this->searcher->in($column, $value);
        return $this;
    }

    public function notIn($column, $value)
    {
        $this->searcher->notIn($column, $value);
        return $this;
    }

    public function isNull($column)
    {
        $this->searcher->isNull($column);
        return $this;
    }

    public function isNotNull($column)
    {
        $this->searcher->isNotNull($column);
        return $this;
    }

    public function startWith($column, $value)
    {
        $this->searcher->startWith($column, $value);
        return $this;
    }

    public function endWith($column, $value)
    {
        $this->searcher->endWith($column, $value);
        return $this;
    }

    public function contains($column, $value)
    {
        $this->searcher->contains($column, $value);
        return $this;
    }

    public function like($column, $value)
    {
        $this->searcher->like($column, $value);
        return $this;
    }

    public function eq($column, $value)
    {
        $this->searcher->eq($column, $value);
        return $this;
    }

    public function neq($column, $value)
    {
        $this->searcher->neq($column, $value);
        return $this;
    }

    public function lt($column, $value)
    {
        $this->searcher->lt($column, $value);
        return $this;
    }

    public function lte($column, $value)
    {
        $this->searcher->lte($column, $value);
        return $this;
    }

    public function gt($column, $value)
    {
        $this->searcher->gt($column, $value);
        return $this;
    }

    public function gte($column, $value)
    {
        $this->searcher->gte($column, $value);
        return $this;
    }

    public function expr($expr)
    {
        $this->searcher->expr($expr);
        return $this;
    }

    public function exists($value)
    {
        $this->searcher->exists($value);
        return $this;
    }

    public function notExists($value)
    {
        $this->searcher->notExists($value);
        return $this;
    }

    public function between($column, $begin, $end)
    {
        $this->searcher->between($column, $begin, $end);
        return $this;
    }

    public function toSql($params = array())
    {
        return $this->searcher->toSql($params);
    }

    public function limit($limit = null , $offset = 0)
    {
        $this->searcher->limit($limit, $offset);
        return $this;
    }

    public function orderAsc($column)
    {
        $this->searcher->orderAsc($column);
        return $this;
    }

    public function orderDesc($column)
    {
        $this->searcher->orderDesc($column);
        return $this;
    }
    // - \Labi\Database\Searcher
}
