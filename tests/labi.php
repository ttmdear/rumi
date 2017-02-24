<?php
require __DIR__."/../vendor/autoload.php";

$adapter = new \Labi\Adapters\Mysql('bookstore', array(
    'adapter' => 'mysql',
    'host' => '127.0.0.1',
    'dbname' => 'bookstore',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
));

$searcher = $adapter->searcher();

$searcher
    ->from('books', 'b')
    ->column('imie')
    ->context()
    ->eq('w1', 123)
    ->brackets(function($searcher){

        $searcher->orOperator();
        $searcher->eq('w2', 123);
        $searcher->eq('w2', 123);
    })
;

echo "sercher : \n--------------\n\n";
echo $searcher->toSql();

$searcher2 = clone($searcher);
$searcher2
    ->column('imie')->hide()
    ->column('imie')->isNotNull()
;

echo "\n\nsercher2 : \n--------------\n\n";
echo $searcher2->toSql();

