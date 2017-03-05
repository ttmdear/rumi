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

class Record extends \Rumi\Orm\Record
{
    public function save()
    {
        $adapter = $this->adapter();
        $definition = $this->definition();
        $autoincrement = $definition->autoincrement();
        $target = $this->target();

        if ($this->stateIs(\Rumi\Orm\RecordInterface::STATE_NEW)) {
            $adapter->create($target, $this->data(true));

            $autoincrement = $definition->autoincrement();

            foreach ($definition->pk() as $pk) {
                if (array_key_exists($pk, $autoincrement)) {
                    if (!$this->defined($pk)) {
                        $lastId = $this->lastInsertId($autoincrement[$pk], $pk);
                        $this->set($pk, $lastId);
                    }
                }
            }
        }else{
            $adapter->update($target, $this->id(), $this->data(true));
        }

        // ustawiam stan na zmodyfikowany
        $this->state(\Rumi\Orm\RecordInterface::STATE_MODYFIED);

        // odswiezam wiersz
        $this->reload();
    }

    public static function definitionClass()
    {
        return \Rumi\Adapters\Pgsql\Definition::class;
    }

    private function lastInsertId($autoincrement, $column)
    {
        $sequence = $autoincrement['sequence'];
        $adapter = $this->adapter();

        if (is_null($sequence)) {
            $target = $this->target();

            $rows =  $adapter->fetch("select currval(pg_get_serial_sequence('$target', '$column')) as newid");

            return $rows[0]['newid'];
        }

        $rows =  $adapter->fetch("select currval('$sequence') as newId");

        return $rows[0]['newId'];
    }
}
