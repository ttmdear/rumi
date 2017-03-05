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

class Column
{
    private $column;
    private $context;

    public function __construct($context, $column)
    {
        $this->column = $column;
        $this->context = $context;
    }

    // + \Labi\Database\Utility\Column
    public function hide()
    {
        $this->column->hide();
        return $this;
    }

    public function show()
    {
        $this->column->show();
        return $this;
    }

    public function isHidden()
    {
        return $this->column->isHidden();
    }

    public function alias($alias = null)
    {
        $this->column->alias($alias);
        return $this;
    }

    public function string($value)
    {
        $this->column->string($value);
        return $this;
    }

    public function value($value = self::UNDEFINED)
    {
        $this->column->value($value);
        return $this;
    }

    public function context()
    {
        return $this->context;
    }

    public function column($cname, $show = true)
    {
        return $this->context->column($cname, $show);
    }

    public function brackets($function)
    {
        $this->column->brackets($function);
        return $this;
    }

    public function andOperator()
    {
        $this->column->andOperator();
        return $this;
    }

    public function orOperator()
    {
        $this->column->orOperator();
        return $this;
    }

    public function in($value)
    {
        $this->column->in($value);
        return $this;
    }

    public function notIn($value)
    {
        $this->column->notIn($value);
        return $this;
    }

    public function isNull()
    {
        $this->column->isNull();
        return $this;
    }

    public function isNotNull()
    {
        $this->column->isNotNull();
        return $this;
    }

    public function startWith($value)
    {
        $this->column->startWith($value);
        return $this;
    }

    public function endWith($value)
    {
        $this->column->endWith($value);
        return $this;
    }

    public function contains($value)
    {
        $this->column->contains($value);
        return $this;
    }

    public function like($value)
    {
        $this->column->like($value);
        return $this;
    }

    public function eq($value)
    {
        $this->column->eq($value);
        return $this;
    }

    public function neq($value)
    {
        $this->column->neq($value);
        return $this;
    }

    public function lt($value)
    {
        $this->column->lt($value);
        return $this;
    }

    public function lte($value)
    {
        $this->column->lte($value);
        return $this;
    }

    public function gt($value)
    {
        $this->column->gt($value);
        return $this;
    }

    public function gte($value)
    {
        $this->column->gte($value);
        return $this;
    }

    public function between($begin, $end)
    {
        $this->column->between($begin, $end);
        return $this;
    }
}
