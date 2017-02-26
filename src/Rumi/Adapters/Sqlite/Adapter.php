<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Adapters\Sqlite;

class Adapter extends \Labi\Adapters\Sqlite\Adapter implements \Rumi\Adapters\AdapterInterface
{
    private $recordsPool;

    public function __construct($name, $config)
    {
        parent::__construct($name, $config);

        // Wykorzystuje klase RecordsPool do przetrzymywania instacji recordow
        $this->recordsPool = new \Rumi\Orm\RecordsPool();
    }

    // + magic
    public function __debugInfo()
    {
        return array(
            'recordsPool' => $this->recordsPool
        );
    }
    // - magic

    // + AdapterInterface
    public function execute($command, $params = array())
    {
        return parent::execute($command, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($command, $params = array())
    {
        return parent::fetch($command, $params);
    }

    public function searcher($searcherClass = null)
    {
        $searcher = null;

        if (is_null($searcherClass)) {
            // wykorzystuje standardowy searcher
            $searcher = new \Rumi\Adapters\Sqlite\Searcher($this);
        } else {
            $searcher = new $searcherClass($this);
        }

        // ustawiam ze klase do obslugi record
        $searcher->recordClass(\Rumi\Adapters\Sqlite\Record::class);

        return $searcher;
    }

    public function create($target, $data)
    {
        // pobieram obiekt odpowiedzialny za tworzenie nowego recordu, Labi
        // obsluguje to przez Creatora
        $creator = $this->creator();

        // // tworze nowy wiersz
        $creator
            ->table($target)
            ->columns(array_keys($data))
            ->add($data)
            ->create();

        return $this;
    }

    public function remove($target, $id)
    {
        $remover = $this->remover();
        $remover->table($target);

        foreach ($id as $column => $value) {
            $remover->eq($column, $value);
        }

        $remover->remove();

        return $this;
    }

    public function update($target, $id, $data)
    {
        $updater = $this->updater();
        $updater
            ->table($target)
            ->values($data);

        foreach ($id as $column => $value) {
            $updater->eq($column, $value);
        }

        $updater->update();

        return $this;
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
    // - AdapterInterface

    /**
     * Zwraca ostatnią wartość dla AUTO_INCREMENT. Działa tylko dla Sqlite.
     */
    public function lastInsertId()
    {
        $result = $this->fetch('select last_insert_rowid() as lastInsertId');

        if (empty($result)) {
            return null;
        }

        return $result[0]['lastInsertId'];
    }
}
