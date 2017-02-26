<?php
require __DIR__."/../vendor/autoload.php";

$adapter = new \Rumi\Adapters\Sqlite\Adapter('bookstore', array(
    'path' => './bookstore.sqlite'
));

$adaptersPool = new \Rumi\Adapters\AdaptersPool(array(
    array(
        'name' => 'bookstore',
        'adapter' => 'mysql',

        'host' => '192.168.10.115',
        'dbname' => 'bookstore',
        'username' => 'user',
        'password' => '',
        'charset' => 'utf8'
    )
));

\Rumi\Orm\Record::adapters($adaptersPool);

class Book extends \Rumi\Adapters\Mysql\Record
{
    protected static $metadata = array(
        'source' => 'bookstore',
        'target' => 'books',
        'definition' => array(
            'idBook' => array(
                'pk',
                'autoincrement',
                'default' => null
            ),
            'name' => array(),
            'idCategory' => array(),
            'releaseDate' => array(),
            'releaseDatetime' => array(),
            'price' => array(),
        )
    );
}

function randomDate() {
    $y = rand(1900, 2010);
    $m = rand(1, 12);
    $d = rand(1, 28);
    $h = rand(1, 23);
    $i = rand(1, 59);
    $s = rand(1, 59);

    return date("$y-$m-$d $h:$i:$s");
}

for ($i=0; $i < 10; $i++) {
    $book = new Book();

    $idCategory = rand(1,13);

    if (in_array($idCategory, array(12,13))) {
        $idCategory = null;
    }

    $book
        ->set('name', "name - {$i}")
        ->set('idCategory', $idCategory)
        ->set('releaseDate', randomDate())
        ->set('releaseDatetime', randomDate())
        ->set('price', (rand(1,10000)/100))
    ;

    $book->save();
}
