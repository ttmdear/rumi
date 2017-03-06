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

class Adapter extends \Rumi\Adapters\AdapterAbstract
{
    private $adapter;

    public function __construct($name, $config)
    {
        parent::__construct();
        $this->adapter = new \Labi\Adapters\Mysql\Adapter($name, $config);
    }

    // + \Rumi\Adapters\AdapterInterface
    public function execute($command, $params = array())
    {
        return $this->adapter->execute($command, $params);
    }

    public function fetch($command, $params = array(), $options = array())
    {
        return $this->adapter->fetch($command, $params, $options);
    }

    public function searcher($searcherClass = null)
    {
        $searcher = null;

        if (is_null($searcherClass)) {
            // wykorzystuje standardowy searcher
            $searcher = new \Rumi\Adapters\Mysql\Searcher($this, $this->adapter->searcher());
        } else {
            $searcher = new $searcherClass($this, $this->adapter->searcher());
        }

        // ustawiam ze klase do obslugi record
        $searcher->recordClass(\Rumi\Adapters\Mysql\Record::class);

        return $searcher;
    }

    public function create($target, $data)
    {
        // pobieram obiekt odpowiedzialny za tworzenie nowego recordu, Labi
        // obsluguje to przez Creatora
        $creator = $this->adapter->creator();

        // // tworze nowy wiersz
        $creator
            ->table($target)
            ->columns(array_keys($data))
            ->add($data)
            ->create()
        ;

        return $this;
    }

    public function remove($target, $id)
    {
        $remover = $this->adapter->remover();
        $remover->table($target);

        foreach ($id as $column => $value) {
            $remover->column($column)->eq($value);
        }

        $remover->remove();

        return $this;
    }

    public function update($target, $id, $data)
    {
        $updater = $this->adapter->updater();
        $updater
            ->table($target)
            ->values($data);

        foreach ($id as $column => $value) {
            $updater->column($column)->eq($value);
        }

        $updater->update();

        return $this;
    }
    // - \Rumi\Adapters\AdapterInterface
}
