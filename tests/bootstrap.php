<?php
require_once __DIR__."/../vendor/autoload.php";

require_once __DIR__."/TestCase.php";

switch ('sqlite') {
case 'mysql':
    require_once __DIR__."/bookstore/src/Book-mysql.php";
    require_once __DIR__."/bookstore/src/BookSearcher-mysql.php";

    // inicjowanie adapterow
    $adapters = new \Rumi\Adapters\AdaptersPool(array(
        array(
            'name' => 'bookstore',
            'adapter' => 'mysql',
            'dbname' => 'bookstore',

            // 'host' => '192.168.10.115',
            // 'username' => 'user',
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8'
        )
    ));

    \Rumi\Orm\Record::adapters($adapters);
    \Rumi\Tests\TestCase::adapters($adapters);

    break;
case 'sqlite':
    require_once __DIR__."/bookstore/src/Book-sqlite.php";
    require_once __DIR__."/bookstore/src/BookSearcher-sqlite.php";

    // inicjowanie adapterow
    $adapters = new \Rumi\Adapters\AdaptersPool(array(
        array(
            'name' => 'bookstore',
            'adapter' => 'sqlite',
            'path' => './bookstore.sqlite'
        ),
    ));

    \Rumi\Orm\Record::adapters($adapters);
    \Rumi\Tests\TestCase::adapters($adapters);

    break;
case 'pgsql':
    require_once __DIR__."/bookstore/src/Book-pgsql.php";
    require_once __DIR__."/bookstore/src/BookSearcher-pgsql.php";

    // inicjowanie adapterow
    $adapters = new \Rumi\Adapters\AdaptersPool(array(
        array(
            'name' => 'bookstore',
            'adapter' => 'pgsql',

            'host' => 'localhost',
            'dbname' => 'bookstore',
            'username' => 'postgres',
            'password' => 'admin',
            'charset' => 'utf8',
        ),
    ));

    \Rumi\Orm\Record::adapters($adapters);
    \Rumi\Tests\TestCase::adapters($adapters);

    break;
}

