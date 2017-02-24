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

            if (count($definition->pk()) === 1 && !is_null($autoincrement)) {
                if (is_null($this->get($autoincrement))) {
                    $lastId = $adapter->lastInsertId();
                    $this->set($autoincrement, $lastId);
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
        return \Rumi\Adapters\Mysql\Definition::class;
    }
}
