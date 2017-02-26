<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Adapters;

abstract class AdapterAbstract implements \Rumi\Adapters\AdapterInterface
{
    private $records = array();

    function __construct()
    {
        $this->recordsPool = new RecordsPool();
    }

    public function registerRecord($target, $id, \Rumi\Orm\RecordInterface $record, $collectionId = null)
    {
        $this->recordsPool->registerRecord($target, $id, $record, $collectionId);
        return $this;
    }

    public function findRecord($target, $id)
    {
        return $this->recordsPool->findRecord($target, $id);
    }

    public function unregisterUse($collectionId)
    {
        $this->recordsPool->unregisterUse($collectionId);
        return $this;
    }
}
