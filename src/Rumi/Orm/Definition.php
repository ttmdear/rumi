<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Orm;

class Definition
{
    private $definition;

    function __construct($definition)
    {
        $this->definition = $definition;
    }

    public function pk()
    {
        $pk = array();

        foreach ($this->definition as $column => $definition) {
            if (in_array('pk', $definition)) {
                $pk[] = $column;
            }
        }

        return $pk;
    }

    public function columns($extends = true)
    {
        $columns = array();

        foreach ($this->definition as $column => $definition) {
            if (!$extends) {
                if (in_array('extends', $definition)) {
                    continue;
                }
            }

            $columns[] = $column;
        }

        return $columns;
    }

    public function column($column)
    {
        if (!array_key_exists($column, $this->definition)) {
            // sprawdzam czy kolumna jest zdefiniowana
            throw new \Exception(printf("Column %s is not defined.", $column));
        }

        return $this->definition[$column];
    }

    public function defaultValue($column)
    {
        if (!$this->hasDefault($column)) {
            throw new \Exception(printf("No default value for %s.", $column));
        }

        $column = $this->column($column);

        return $column['default'];
    }

    public function hasDefault($column)
    {
        return array_key_exists('default', $this->column($column));
    }

    public function isDefined($column)
    {
        return array_key_exists($column, $this->definition);
    }

    public function convType($column, $value)
    {
        return $value;
    }
}
