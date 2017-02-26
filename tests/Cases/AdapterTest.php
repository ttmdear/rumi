<?php
namespace Rumi\Tests\Cases;

use Rumi\Tests\Bookstore\Book;
use Rumi\Tests\Bookstore\BookSearcher;

use Rumi\Orm\SearcherInterface;
use Rumi\Adapters\AdapterInterface;

class AdapterTest extends \Rumi\Tests\TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AdapterInterface::class, self::adapter('bookstore'));
    }

    public function testExecute()
    {
        $this->reset();

        $bookstore = self::adapter('bookstore');

        if ($bookstore instanceof \Rumi\Adapters\Mysql\Adapter) {
            $bookstore->execute('call createBooks(:num)', array(
                'num' => 10
            ));

            $num = Book::searcher()->all()->count();
            $this->assertEquals(20, $num);
        }else{
            return;
        }
    }

    public function testFetch()
    {
        $this->reset();

        $bookstore = self::adapter('bookstore');

        $rows = $bookstore->fetch("select name from books;");
        $this->assertEquals(count($rows), 10);

        $rows = $bookstore->fetch("select name from books where 1=2;");

        $this->assertEquals(is_array($rows), true);
        $this->assertEquals(count($rows), 0);
    }

    public function testSearcher()
    {
        $bookstore = self::adapter('bookstore');

        $this->assertInstanceOf(\Rumi\Orm\SearcherInterface::class, $bookstore->searcher());
        $this->assertInstanceOf(BookSearcher::class, $bookstore->searcher(BookSearcher::class));
    }

    public function testCreate()
    {
        $this->reset();

        $bookstore = self::adapter('bookstore');
        $releaseDate = date('Y-m-d');
        $releaseDatetime = date('Y-m-d H:i:s');

        $bookstore->create('books', array(
            'idBook' => 255,
            'name' => 'test',
            'idCategory' => null,
            'releaseDate' => $releaseDate,
            'releaseDatetime' => $releaseDatetime,
            'price' => 11,
        ));

        $searcher = $bookstore->searcher();
        $searcher
            ->target('books')
            ->id(array(
                'idBook' => 255
            ))
        ;

        $rows = $searcher->fetch();

        $this->assertEquals(is_array($rows), true);
        $this->assertEquals(count($rows), 1);

        $row = $rows[0];

        $this->assertEquals($row['idBook'], 255);
        $this->assertEquals($row['name'], 'test');
        $this->assertEquals($row['idCategory'], null);
        $this->assertEquals($row['releaseDate'], $releaseDate);
        $this->assertEquals($row['releaseDatetime'], $releaseDatetime);
        $this->assertEquals($row['price'], 11);
    }

    public function testRemove()
    {
        $this->reset();

        $bookstore = self::adapter('bookstore');
        $bookstore->remove('books', array(
            'idBook' => 1
        ));

        $searcher = $bookstore->searcher();
        $searcher
            ->target('books')
        ;

        $rows = $searcher->fetch();

        $this->assertEquals(is_array($rows), true);
        $this->assertEquals(count($rows), 9);
    }

    public function testUpdate()
    {
        $this->reset();

        $bookstore = self::adapter('bookstore');
        $releaseDate = date('Y-m-d');
        $releaseDatetime = date('Y-m-d H:i:s');

        $bookstore->update('books', array(
            'idBook' => 5,
        ), array(
            'name' => 'test',
            'idCategory' => null,
            'releaseDate' => $releaseDate,
            'releaseDatetime' => $releaseDatetime,
            'price' => 11,
        ));

        $searcher = $bookstore->searcher();
        $searcher
            ->target('books')
            ->id(array(
                'idBook' => 5
            ))
        ;

        $rows = $searcher->fetch();

        $this->assertEquals(is_array($rows), true);
        $this->assertEquals(count($rows), 1);

        $row = $rows[0];

        $this->assertEquals($row['idBook'], 5);
        $this->assertEquals($row['name'], 'test');
        $this->assertEquals($row['idCategory'], null);
        $this->assertEquals($row['releaseDate'], $releaseDate);
        $this->assertEquals($row['releaseDatetime'], $releaseDatetime);
        $this->assertEquals($row['price'], 11);

    }

    public function testRegisterRecord()
    {
        $bookstore = self::adapter('bookstore');
        $book = new Book();
        $collectionIdA = uniqid()."a";
        $collectionIdB = uniqid()."b";

        $target = 'books';
        $id = array('idBook' => 10);

        // registerRecord($target, $id, RecordInterface $record, $collectionId = null);
        $bookstore->registerRecord($target, $id, $book, $collectionIdA);
        $bookstore->registerRecord($target, $id, $book, $collectionIdB);

        $this->assertEquals($book, $bookstore->findRecord($target, $id));

        $bookstore->unregisterUse($collectionIdA);
        $this->assertEquals($book, $bookstore->findRecord($target, $id));

        $bookstore->unregisterUse($collectionIdB);
        $this->assertEquals(null, $bookstore->findRecord($target, $id));
    }
}
