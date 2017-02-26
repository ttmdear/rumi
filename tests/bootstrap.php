<?php
require_once __DIR__."/../vendor/autoload.php";

require_once __DIR__."/TestCase.php";

// Bookstore
require_once __DIR__."/bookstore/src/Book.php";
require_once __DIR__."/bookstore/src/BookSearcher.php";

// inicjowanie adapterow
$adapters = new \Rumi\Adapters\AdaptersPool(array(
    array(
        'name' => 'bookstore',
        'adapter' => 'sqlite',
        'path' => './bookstore.sqlite'
    ),
    // array(
    //     'name' => 'bookstore',
    //     'adapter' => 'mysql',
    //     'dbname' => 'bookstore',

    //     // 'host' => '192.168.10.115',
    //     // 'username' => 'user',
    //     'host' => 'localhost',
    //     'username' => 'root',
    //     'password' => '',
    //     'charset' => 'utf8'
    // )
));

\Rumi\Orm\Record::adapters($adapters);

\Rumi\Tests\TestCase::adapters($adapters);
