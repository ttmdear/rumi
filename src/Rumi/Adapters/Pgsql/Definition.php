<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Adapters\Pgsql;

class Definition extends \Rumi\Orm\Definition
{
    /**
     * Zwraca kolumne zdefiniowana jako autoincrement.
     *
     * @return null|string
     */
    public function autoincrement()
    {
        $columns = $this->columns();

        $autoincrements = array();

        foreach ($columns as $column) {
            $definition = $this->column($column);

            if (in_array('autoincrement', $definition)) {
                // mamy zdefiniowana tylko flage autoincrement na kolumnie, bez
                // danych
                $autoincrements[$column] = array(
                    'sequence' => null,
                );

                continue;
            }

            if (array_key_exists('autoincrement', $definition)) {
                if (!is_array($definition['autoincrement'])) {
                    throw new \Exception("Incorrect definition of autoincrement for {$column} column should be array(sequence => 'sequenceName')");
                }

                $autoincrements[$column] = array_merge(array('sequence' => null), $definition['autoincrement']);
            }
        }

        return $autoincrements;
    }
}
