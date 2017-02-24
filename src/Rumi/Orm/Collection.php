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

class Collection implements \IteratorAggregate, \Countable
{
    private $adapter;
    private $target;
    private $definition;
    private $records = array();
    private $collectionId;

    private $searcher;

    function __construct(
        \Rumi\Adapters\AdapterInterface $adapter,
        $target,
        Definition $definition,
        $recordClass,
        \Rumi\Orm\SearcherInterface $searcher,
        $records = array()
    ) {
        // generowanie id dla kolekcji
        $this->collectionId = \Rumi\functions\uniqid();

        $this->searcher = $searcher;
        $this->adapter = $adapter;
        $this->target = $target;
        $this->definition = $definition;
        $this->recordClass = $recordClass;
        $this->records = $records;

        foreach ($this->records as $record) {
            if ($record instanceof \Rumi\Orm\RecordInterface) {

                $id = array();
                foreach ($this->definition->pk() as $pk) {
                    if (empty($record[$pk])) {
                        throw new \Exception("Can not create record because value of {$pk} which is part of primary key is empty.");
                    }

                    $id[$pk] = $record->get($pk);
                }

                // rejestruje uzycie encji w adapterze
                $this->adapter->registerRecord($this->target, $id, $record, $this->collectionId);
            }
        }
    }

    public function __destruct()
    {
        $this->adapter->unregisterUse($this->collectionId);
    }

    // + IteratorAggregate
    public function getIterator()
    {
        return new \Rumi\Orm\Collection\Iterator($this);
    }
    // - IteratorAggregate

    // + Countable
    public function count()
    {
        return count($this->records);
    }
    // - Countable

    public function get($index)
    {
        if (!array_key_exists($index, $this->records)) {
            throw new \Exception("Index {$index} does not exists.");
        }

        // pobieram record z danych
        $record = $this->records[$index];

        if ($record instanceof \Rumi\Orm\RecordInterface) {
            // record zostal juz zainicjowany
            return $record;
        }

        // record nie zostal zainicjowany
        $id = array();
        foreach ($this->definition->pk() as $pk) {
            if (empty($record[$pk])) {
                throw new \Exception("Can not create record because value of {$pk} which is part of primary key is empty.");
            }

            $id[$pk] = $record[$pk];
        }

        // sprawdzam czy encja nie zostala juz stworzona przez inna kolekcje
        $recordin = $this->adapter->findRecord($this->target, $id);

        if (is_null($recordin)) {
            // nie znaleziono recordu, wiec tworze nowy
            $recordClass = $this->recordClass;

            // tworze nowa encje
            $recordin = new $recordClass(
                false,
                $this->adapter,
                $this->target,
                $this->definition,
                $this->searcher
            );

            $recordin->load($record);
        }

        // rejestruje uzycie encji w adapterze
        $this->adapter->registerRecord($this->target, $id, $recordin, $this->collectionId);

        // zapisuje w encje lokalnie
        $this->records[$index] = $recordin;

        return $this->records[$index];
    }

    public function first()
    {
        foreach ($this as $record) {
            return $record;
        }

        return null;
    }

    public function save()
    {
        foreach ($this as $record) {
            $record->save();
        }

        return $this;
    }

    public function remove()
    {
        foreach ($this as $record) {
            $record->remove();
        }

        return $this;
    }

    public function call($name, array $args)
    {
        foreach ($this as $record) {
            call_user_func_array(array($record, $name), $args);
        }

        return $this;
    }

    public function filter($filter)
    {
        $records = array();
        $count = $this->count();

        for ($i=0; $i < $count; $i++) {
            if (call_user_func_array($filter, array($this->get($i)))) {
                $records[] = $this->get($i);
            }
        }

        return new static(
            $this->adapter,
            $this->target,
            $this->definition,
            $this->recordClass,
            $this->searcher,
            $records
        );
    }

    public function sort($direct = 'asc', $column = null, $algorithm = 'bubble')
    {
        $records = array();
        $count = $this->count();

        if ($count < 2) {
            // jesli kolekcja zwiera 1 lub 0 elementow
            return $this;
        }

        for ($i=0; $i < $count; $i++) {
            $records[] = $this->get($i);
        }

        $changed = true;
        while(true){
            $changed = false;

            for ($i=0; $i < $count - 1; $i++) {
                $j = $i+1;
                $recordA = $records[$i];
                $recordB = $records[$j];

                $r = $recordA->compareTo($recordB, $column);

                if ($r === 0) {
                    continue;
                }

                if ($r === 1 && $direct === 'asc') {
                    $records[$i] = $recordB;
                    $records[$j] = $recordA;

                    $changed = true;
                    continue;
                }

                if ($r === -1 && $direct === 'desc') {
                    $records[$i] = $recordB;
                    $records[$j] = $recordA;

                    $changed = true;
                    continue;
                }
            }

            if ($changed === false) {
                break;
            }
        }

        return new static(
            $this->adapter,
            $this->target,
            $this->definition,
            $this->recordClass,
            $this->searcher,
            $records
        );
    }

    public function group($columns)
    {
        if (is_string($columns)) {
            $columns = array($columns);
        }

        $collections = array();

        foreach ($this as $record) {
            $index = null;

            if (is_array($columns)) {
                $index = array();
                foreach ($columns as $column) {
                    $index[] = $record->get($column);
                    //$index[$column] = $record->get($column);
                }
            }elseif(is_callable($columns)){
                $index = call_user_func_array($columns, array($record));
            }else{
                throw new \Exception(printf("Invalid group.", implode(',', $index)));
            }

            $key = md5(var_export($index, true));

            if (!array_key_exists($key, $collections)) {
                $collections[$key] = array(
                    'index' => $index,
                    'records' => array()
                );
            }

            $collections[$key]['records'][] = $record;
        }

        $aggregator = new \Rumi\Orm\Collection\Aggregator();

        foreach ($collections as $key => $collection) {
            $aggregator->add($collection['index'], new static(
                $this->adapter,
                $this->target,
                $this->definition,
                $this->recordClass,
                $this->searcher,
                $collection['records']
            ));
        }

        return $aggregator;
    }

    // Funkcja takie jak SUM, AV, MAX, MIN itp. powinny byc przeniesionone do
    // osobnej klasy ktora wspolprawcowala by z Collection, np.
    // $collection->functions();
    //
    // Inne funckje takie jak, sort, sortBy, middle, top, tail, cat itp
    // public function sum($column)
    // {
    //     $sum = 0;
    //     $count = $this->count();

    //     for ($i=0; $i < $count; $i++) {
    //         $num = $this->get($i)->get($column);
    //         if (is_numeric($num)) {
    //             throw new \Exception("Can not count sum for non numeric value {$num}.");
    //         }

    //         $sum += $num;
    //     }

    //     return $sum;
    // }
}
