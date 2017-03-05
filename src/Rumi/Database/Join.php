<?php
/*
 * This file is part of the Labi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Database;

use Labi\Database\Utility\Condition;
use Labi\Database\Utility\ConditionInterface;

class Join implements ConditionInterface
{
    private $join;
    private $context;

    function __construct($context, $join)
    {
        $this->context = $context;
        $this->join = $join;
    }

    // + \Labi\Database\Utility\Join
    public function param($name, $value)
    {
        $this->join->param($name, $value);
        return $this;
    }

    public function params()
    {
        return $this->join->params();
    }

    public function alias()
    {
        return $this->join->alias();
    }

    public function table()
    {
        return $this->join->table();
    }

    public function context()
    {
        return $this->context;
    }

    public function column($cname, $show = true)
    {
        return new \Rumi\Database\Column($this, $this->join->column($cname, $show));
    }

    public function typeInner()
    {
        $this->join->typeInner();
        return $this;
    }

    public function typeLeft()
    {
        $this->join->typeLeft();
        return $this;
    }

    public function typeOuter()
    {
        $this->join->typeOuter();
        return $this;
    }

    public function innerJoin($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->join->innerJoin($table, $alias = null));
    }

    public function outerJoin($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->join->outerJoin($table, $alias = null));
    }

    public function leftJoin($table, $alias = null)
    {
        return new \Rumi\Database\Join($this->join->leftJoin($table, $alias = null));
    }

    public function join($table, $alias = null)
    {
        return new \Rumi\Database\Join($this, $this->join->join($table, $alias = null));
    }

    public function using($using)
    {
        $this->join->using($using);
        return $this;
    }

    public function brackets($function, $scope = null)
    {
        if (is_null($scope)) {
            $scope = $this;
        }

        $this->join->brackets($function, $scope);
        return $this;
    }

    public function andOperator()
    {
        $this->join->andOperator();
        return $this;
    }

    public function orOperator()
    {
        $this->join->orOperator();
        return $this;
    }

    public function in($column, $value)
    {
        $this->join->in($column, $value);
        return $this;
    }

    public function notIn($column, $value)
    {
        $this->join->notIn($column, $value);
        return $this;
    }

    public function isNull($column)
    {
        $this->join->isNull($column);
        return $this;
    }

    public function isNotNull($column)
    {
        $this->join->isNotNull($column);
        return $this;
    }

    public function startWith($column, $value)
    {
        $this->join->startWith($column, $value);
        return $this;
    }

    public function endWith($column, $value)
    {
        $this->join->endWith($column, $value);
        return $this;
    }

    public function contains($column, $value)
    {
        $this->join->contains($column, $value);
        return $this;
    }

    public function like($column, $value)
    {
        $this->join->like($column, $value);
        return $this;
    }

    public function eq($column, $value)
    {
        $this->join->eq($column, $value);
        return $this;
    }

    public function neq($column, $value)
    {
        $this->join->neq($column, $value);
        return $this;
    }

    public function lt($column, $value)
    {
        $this->join->lt($column, $value);
        return $this;
    }

    public function lte($column, $value)
    {
        $this->join->lte($column, $value);
        return $this;
    }

    public function gt($column, $value)
    {
        $this->join->gt($column, $value);
        return $this;
    }

    public function gte($column, $value)
    {
        $this->join->gte($column, $value);
        return $this;
    }

    public function expr($expr)
    {
        $this->join->expr($expr);
        return $this;
    }

    public function exists($value)
    {
        $this->join->exists($value);
        return $this;
    }

    public function notExists($value)
    {
        $this->join->notExists($value);
        return $this;
    }

    public function between($column, $begin, $end)
    {
        $this->join->between($column, $begin, $end);
        return $this;
    }
}
