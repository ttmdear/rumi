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

use Rumi\Orm\RecordInterface;
use Rumi\Orm\Definition;
use Rumi\Orm\SearcherInterface;

abstract class Record implements RecordInterface, \ArrayAccess
{
    abstract public function save();

    protected static $metadata = null;

    private static $adapters = null;

    private $adapter;
    private $target;
    private $definition;
    private $data = array();
    private $mdata = array();
    private $state;

    private $searcher;
    private $rsearcher = null;

    public function __construct(
        $readMetadata = true,
        \Rumi\Adapters\AdapterInterface $adapter = null,
        $target = null,
        Definition $definition = null,
        SearcherInterface $searcher = null
    ) {
        if ($readMetadata) {
            // inicjuje record na postawie informacji z metadanych
            $metadata = static::metadata();

            $adapter = static::adapters()->get($metadata->source());

            $this->adapter = $adapter;
            $this->target = $metadata->target();
            $this->definition = $metadata->definition();
            $this->searcher = static::searcher();
        }else{
            // inaczej re parametry powinny byc przekazane przez konstruktor,
            // nie sprawdzam z powodow wydajnosciowych, np. przy tworzeniu
            // nowych wierszy
            $this->adapter = $adapter;
            $this->target = $target;
            $this->definition = $definition;
            $this->searcher = $searcher;
        }

        // ustawiam stan jako nowy
        $this->state(RecordInterface::STATE_NEW);
    }

    public static function searcher()
    {
        $metadata = static::metadata();

        $definition = $metadata->definition();
        $adapters = static::adapters();
        $adapter = $adapters->get($metadata->source());

        $searcher = $adapter->searcher($metadata->searcherClass());
        $searcher->recordClass(static::class);

        $searcher->target($metadata->target());

        // wpinam obiekt definicji
        $searcher->definition($definition);
        foreach ($definition->columns(false) as $column) {
            $searcher->column($column);
        }

        static::extendSearcher($searcher);

        return $searcher;
    }

    public static function adapters($adapters = null)
    {
        if (is_null($adapters)) {
            if (is_null(self::$adapters)) {
                throw new \Exception(printf("There are no adapters, use Record\:\:adapters() to set AdaptersPool."));
            }

            return self::$adapters;
        }

        static::$adapters = $adapters;
    }

    public static function metadata()
    {
        return new \Rumi\Orm\Metadata(static::class, static::definitionClass(), static::$metadata);
    }

    public static function extendSearcher(SearcherInterface $searcher)
    {
        return $searcher;
    }

    // + magic
    public function __isset($name)
    {
        if (array_key_exists($column, $this->data)) {
            return true;
        }

        if (!$this->definition->isDefined($column)) {
            // kolumna nie jest zdefiniowania
            return false;
        }

        if (!$this->definition->hasDefault($column)) {
            // kolumna jest zdefiniowana ale nie posiada wartosci default
            return false;
        }

        // kolmna jest zdefiniowana i posiada wartosc default, czyli istnieje
        return true;
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
    // - magic

    public function adapter($adapter = null)
    {
        if (is_null($adapter)) {
            return $this->adapter;
        }

        $this->adapter = $adapter;
        return $this;
    }

    public function target($target = null)
    {
        if (is_null($target)) {
            return $this->target;
        }

        $this->target = $target;
        return $this;
    }

    public function definition($definition = null)
    {
        if (is_null($definition)) {
            return $this->definition;
        }

        $this->definition = $definition;
        return $this;
    }

    public static function definitionClass()
    {
        return Definition::class;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;

        if (!$this->stateIs(RecordInterface::STATE_NEW)) {
            $this->state(RecordInterface::STATE_MODYFIED);
        }

        return $this;
    }

    public function compareTo(\Rumi\Orm\RecordInterface $record, $column = null)
    {
        throw new \Exception(printf("Comparable is not supported for %s", static::class));
    }

    public function get($column)
    {
        if (array_key_exists($column, $this->data)) {
            // pole jest zdefiniowane, wiec zwracam zwracam bezposrednio dane
            return $this->data[$column];
        }

        if (!$this->definition->isDefined($column)) {
            throw new \Exception(printf("Column %s is not defined.", $column));
        }

        if (!$this->definition->hasDefault($column)) {
            throw new \Exception(printf("Column %s is not set and do not have default value.", $column));
        }

        return $this->definition->defaultValue($column);
    }

    public function defined($column)
    {
        if (array_key_exists($column, $this->data)) {
            return true;
        }

        if (!$this->definition->isDefined($column)) {
            // kolumna nie jest zdefiniowania
            return false;
        }

        if (!$this->definition->hasDefault($column)) {
            // kolumna jest zdefiniowana ale nie posiada wartosci default
            return false;
        }

        // kolmna jest zdefiniowana i posiada wartosc default, czyli istnieje
        return true;
    }

    public function unsetColumn($column)
    {

    }

    public function data($definition = false)
    {
        // sprawdzam jakie sa aktualnie ustawione klucze
        $columns = array_keys($this->data);

        // tworze kontener na dane tymczasowe
        $tmpdata = array();

        // pobieram tylko kolumny z definicji
        $defColumns = $this->definition->columns(false);

        // lacze kolumny z definicji z kolumnami podanymi
        $columns = array_unique(array_merge($defColumns, $columns));

        foreach ($columns as $column) {
            if ($definition) {
                if (!in_array($column, $defColumns)) {
                    continue;
                }
            }

            // metoda get ostatecznie zwraca wartosc
            $tmpdata[$column] = $this->get($column);
        }

        return $tmpdata;
    }

    public function id()
    {
        $id = array();

        foreach ($this->definition->pk() as $pk) {
            $id[$pk] = $this->get($pk);
        }

        return $id;
    }

    public function remove()
    {
        if ($this->stateIs(RecordInterface::STATE_NEW)) {
            return $this;
        }

        $this->adapter->remove($this->target, $this->id());

        $this->state(RecordInterface::STATE_NEW);

        return $this;
    }

    public function reload()
    {
        if ($this->stateIs(RecordInterface::STATE_NEW)) {
            // nie ma potrzeby przeladowania nowego wiersza, nie ma czym go
            // przeladowac
            return $this;
        }

        if (is_null($this->rsearcher)) {
            // tworzymy obiekt searcher dla recordu
            $this->rsearcher = clone($this->searcher);
            $this->rsearcher->id($this->id());
        }

        // pobieram dane
        $data = $this->rsearcher->fetch();

        if (empty($data)) {
            // sytuacja nie powinna miec miejsca
            $id = var_export($this->id());
            throw new \Exception("There is no row for {$this->target} with id {$id}.");
        }

        // wczytuje dane do obiektu
        $this->load($data[0]);

        return $this;
    }

    public function load($data)
    {
        // wczytuje dane podstawowe
        $this->data = $data;

        $this->state(RecordInterface::STATE_SYNCED);

        return $this;
    }

    public function state($state = null)
    {
        if (is_null($state)) {
            return $this->state;
        }

        $this->state = $state;

        return $this;
    }

    public function stateIs($state)
    {
        return $this->state() === $state;
    }

    // + ArrayAccess
    public function offsetSet($column, $value)
    {
        if (is_null($column)) {
            throw new \Exception("Record do not implement set value without column.");
        } else {
            $this->set($column, $value);
        }
    }

    public function offsetExists($column)
    {
        return $this->defined($column);
    }

    public function offsetUnset($column)
    {
        if ($this->defined($column)) {
            $this->unsetColumn($column);
        }

        throw new \Exception("{$column} is not defined.");
    }

    public function offsetGet($column)
    {
        if ($this->defined($column)) {
            return $this->get($column);
        }

        throw new \Exception("{$column} is not defined.");
    }
    // - ArrayAccess
}
