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

abstract class Record implements
    \Rumi\Orm\RecordInterface,
    \ArrayAccess,
    \JsonSerializable
{
    abstract public function save();

    /**
     * protected static $metadata = array(
     *     'source' => 'bookstore',
     *     'target' => 'books',
     *     'definition' => array(
     *         'idBook' => array(
     *             'pk',
     *             'autoincrement'
     *         ),
     *         'name',
     *         'idCategory',
     *         'releaseDate',
     *         'releaseDatetime',
     *         'price',
     *     )
     * );
     */
    protected static $metadata = null;

    private static $adapters = null;

    private $adapter;
    private $target;
    private $definition;
    private $data = array();
    private $mdata = array();
    private $state;

    /**
     * Referencja do searchera z kolekcji ktora zaiinicjowala Record.
     * @var \Rumi\Orm\SearcherInterface
     */
    private $searcher;

    /**
     * Referencja do obiektu searchera ktory pobiera record.
     * @var \Rumi\Orm\SearcherInterface
     */
    private $rsearcher = null;

    public function __construct($readMetadata = true, \Rumi\Adapters\AdapterInterface $adapter = null, $target = null, \Rumi\Orm\Definition $definition = null, \Rumi\Orm\SearcherInterface $searcher = null) {
        if ($readMetadata) {
            // inicjuje record na postawie informacji z metadanych
            $metadata = static::metadata();

            // pobieram adapter na postawie nazwy zrodla
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
        $this->state(\Rumi\Orm\RecordInterface::STATE_NEW);

        $this->prepare();

        // wczytanie pustego nowego wiersza z pustymi danymi, uzytkownikowi
        // zostawiam kwestie wprowadzenia danych
        $this->load(array());
    }

    public static function searcher()
    {
        $metadata = static::metadata();

        $definition = $metadata->definition();
        $adapters = static::adapters();
        $adapter = $adapters->get($metadata->source());

        $searcher = $adapter->searcher(static::searcherClass());
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

    public static function definitionClass()
    {
        return \Rumi\Orm\Definition::class;
    }

    public static function searcherClass()
    {
        return null;
    }

    public static function metadata()
    {
        return new \Rumi\Orm\Metadata(
            static::class,
            static::definitionClass(),
            static::$metadata
        );
    }

    /**
     * Metoda ustawia AdaptersPool z dla recordu.
     *
     * @param \Rumi\Adapters\AdaptersPool
     * @return null|\Rumi\Adapters\AdaptersPool
     */
    public static function adapters($adapters = null)
    {
        if (is_null($adapters)) {
            if (is_null(self::$adapters)) {
                throw new \Exception(sprintf("There are no adapters, use Record\:\:adapters() to set AdaptersPool."));
            }

            return self::$adapters;
        }

        static::$adapters = $adapters;
    }

    public static function extendSearcher(\Rumi\Orm\SearcherInterface $searcher)
    {
        return $searcher;
    }

    public function prepare()
    {

    }

    // + magic
    public function __isset($column)
    {
        return $this->defined($column);
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

    public function set($name, $value)
    {
        $this->data[$name] = $value;

        if (!$this->stateIs(\Rumi\Orm\RecordInterface::STATE_NEW)) {
            $this->state(\Rumi\Orm\RecordInterface::STATE_MODYFIED);
        }

        return $this;
    }

    public function compareTo(\Rumi\Orm\RecordInterface $record, $column = null)
    {
        throw new \Exception(sprintf("Comparable is not supported for %s", static::class));
    }

    public function get($column)
    {
        // if (array_key_exists($column, $this->data)) {
        if (isset($this[$column])) {
            // pole jest zdefiniowane, wiec zwracam zwracam bezposrednio dane
            return $this->data[$column];
        }

        throw new \Exception(sprintf("Column %s is not defined.", $column));
    }

    public function defined($column)
    {
        if (array_key_exists($column, $this->data)) {
            return true;
        }

        return false;
    }

    public function unsetColumn($column)
    {
        unset($this->data[$column]);

        return $this;
    }

    public function data($definition = false)
    {
        // sprawdzam jakie sa aktualnie ustawione klucze
        $columns = array_keys($this->data);

        // tworze kontener na dane tymczasowe
        $tmpdata = array();

        // pobieram tylko kolumny z definicji
        $dcolumns = $this->definition->columns();

        // lacze kolumny z definicji z kolumnami podanymi
        $columns = array_unique(array_merge($dcolumns, $columns));

        foreach ($columns as $column) {
            if ($definition) {
                if (!in_array($column, $dcolumns)) {
                    continue;
                }
            }

            if ($this->defined($column)) {
                // dodaje tylko te kolumny ktore sa zdefiniowane
                $tmpdata[$column] = $this->get($column);
            }
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
        if ($this->stateIs(\Rumi\Orm\RecordInterface::STATE_NEW)) {
            // jak record jest nowy to nie ma co usuwac
            return $this;
        }

        // usuwam
        $this->adapter->remove($this->target, $this->id());

        // ustawiam stan jako nowy
        $this->state(\Rumi\Orm\RecordInterface::STATE_NEW);

        return $this;
    }

    public function reload()
    {
        if ($this->stateIs(\Rumi\Orm\RecordInterface::STATE_NEW)) {
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

        // po przeladowaniu ustawiam flage ze wiersz jest synchronizowany
        $this->state(\Rumi\Orm\RecordInterface::STATE_SYNCED);

        // wczytuje dane do obiektu
        $this->load($data[0]);

        return $this;
    }

    public function load($data)
    {
        // wczytuje dane podstawowe
        $this->data = $data;

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

    // + export
    protected function export()
    {
        return $this->data();
    }

    public function toArray()
    {
        return $this->export();
    }

    public function jsonSerialize()
    {
        return $this->toJson();
    }

    public function toJson()
    {
        $encoded = json_encode($this->export());

        if ($encoded === false) {
            throw new \Exception("Can not create json.");
        }

        return $encoded;
    }

    public function toSql()
    {
        if (is_null($this->rsearcher)) {
            // tworzymy obiekt searcher dla recordu
            $this->rsearcher = clone($this->searcher);
            $this->rsearcher->id($this->id());
        }

        return $this->rsearcher->toSql();
    }
}
