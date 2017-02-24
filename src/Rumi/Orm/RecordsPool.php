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

class RecordsPool
{
    protected $records = array();

    // + magic
    public function __debugInfo()
    {
        $records = array();

        foreach ($records as $collectionId => $records) {
            foreach ($records as $key => $record) {
                $records[$collectionId][$key] = 1;
            }
        }

        return array(
            'records' => $records
        );
    }
    // - magic

    public function registerRecord($target, $id, RecordInterface $record, $collectionId = null)
    {
        if (is_null($collectionId)) {
            $collectionId = "new";
        }

        $key = $this->createKey($target, $id);

        if (!isset($this->records[$collectionId])) {
            $this->records[$collectionId] = array();
        }

        if (isset($this->records[$collectionId][$key])) {
            throw new \Exception("Record should not be registered twice at same collection.");
        }

        $this->records[$collectionId][$key] = $record;

        return $this;
    }

    public function findRecord($target, $id)
    {
        $key = $this->createKey($target, $id);

        foreach (array_keys($this->records) as $collectionId) {
            if (isset($this->records[$collectionId][$key])) {
                return $this->records[$collectionId][$key];
            }
        }

        return null;
    }

    public function unregisterUse($collectionId)
    {
        if (isset($this->records[$collectionId])) {
            unset($this->records[$collectionId]);
        }

        return $this;
    }

    private function createKey($target, $id)
    {
        return md5(var_export($target, true).var_export($id, true));
    }
}
