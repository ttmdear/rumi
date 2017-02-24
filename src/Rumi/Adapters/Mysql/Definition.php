<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Adapters\Mysql;

class Definition extends \Rumi\Orm\Definition
{
    /**
     * Zwraca kolumne zdefiniowana jako autoincrement.
     *
     * @return null|string
     */
    public function autoincrement()
    {
        $columns = $this->columns(false);

        foreach ($columns as $column) {
            $definition = $this->column($column);

            if (in_array('autoincrement', $definition)) {
                return $column;
            }
        }

        return null;
    }
}
