<?php
namespace Rumi\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    private static $adapters;

    public function adapters($adapters = null)
    {
        if (is_null($adapters)) {
            return self::$adapters;
        }

        self::$adapters = $adapters;
    }

    public function adapter($name)
    {
        return self::adapters()->get($name);
    }

    public function reset()
    {
        $bookstore = self::$adapters->get('bookstore');

        if ($bookstore instanceof \Rumi\Adapters\Sqlite\Adapter) {
            $bookstore->execute(file_get_contents(__DIR__."./bookstore/doc/bookstore-sqlite.sql"));
        }elseif($bookstore instanceof \Rumi\Adapters\Mysql\Adapter){
            $bookstore->execute(file_get_contents(__DIR__."./bookstore/doc/bookstore-mysql.sql"));
        }
    }

    public function assertRows($ids, $rows, $table = 'books')
    {
        $index = array();

        foreach ($ids as $id) {
            $index[$id] = true;
        }

        foreach ($rows as $row) {
            $id = null;

            switch ($table) {
            case 'books':
                $this->assertEquals(isset($row['idBook']), true);
                $id = $row['idBook'];

                break;

            default:
                throw new \Exception("Zly typ tabeli.");

                break;
            }

            unset($index[$id]);
        }

        $this->assertEquals(count($index), 0);
    }

    public function md5($var)
    {
        return md5(var_export($var, true));
    }

    public function randomDate() {
        $y = rand(1900, 2010);
        $m = rand(1, 12);
        $d = rand(1, 28);
        $h = rand(1, 23);
        $i = rand(1, 59);
        $s = rand(1, 59);

        return date("$y-$m-$d");
    }

    public function randomDatetime() {
        $y = rand(1900, 2010);
        $m = rand(1, 12);
        $d = rand(1, 28);
        $h = rand(1, 23);
        $i = rand(1, 59);
        $s = rand(1, 59);

        return date("$y-$m-$d $h:$i:$s");
    }
}
