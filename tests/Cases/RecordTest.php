<?php
namespace Rumi\Tests\Cases;

use Rumi\Tests\Bookstore\Book;
use Rumi\Orm\SearcherInterface;
use Rumi\Orm\RecordInterface;
use Rumi\Orm\Collection;

class RecordTest extends \Rumi\Tests\TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(RecordInterface::class, new Book());
        $this->assertInstanceOf(RecordInterface::class, Book::searcher()->first());
    }

    public function testDefaultValues()
    {
        $this->reset();

        $book = new Book();

        $this->assertEquals($book->get('idBook'), null);
        $this->assertEquals($book->get('idCategory'), null);
    }

    public function testCreate()
    {
        $this->reset();

        $name = 'name - 10';
        $releaseDate = date('Y-m-d');
        $releaseDatetime = date('Y-m-d H:i:s');
        $price = 10.00;

        $book = new Book();
        $book->set('name', $name);
        $book->set('releaseDate', $releaseDate);
        $book->set('releaseDatetime', $releaseDatetime);
        $book->set('price', $price);
        $book->save();

        $idBook = $book->get('idBook');

        // get created row
        $books = Book::searcher();
        $books->eq('idBook', $idBook);

        $book = $books->first();

        $this->assertEquals($book->get('idBook'), $idBook);
        $this->assertEquals($book->get('name'), $name);
        $this->assertEquals($book->get('releaseDate'), $releaseDate);
        $this->assertEquals($book->get('releaseDatetime'), $releaseDatetime);
        $this->assertEquals($book->get('price'), $price);
    }

    public function testUpdate()
    {
        $this->reset();

        define('debug', 1);
        $books = Book::searcher();
        $books->id(array(
            'idBook' => 1
        ));

        $book = $books->first();

        $book->set('name', 'test123');

        $book->save();

        unset($books);

        $books = Book::searcher();
        $books->id(array(
            'idBook' => 1
        ));

        $book = $books->first();

        $this->assertEquals($book->get('name'), 'test123');
    }

    public function testRemove()
    {
        $this->reset();

        $books = Book::searcher();
        $books->id(array(
            'idBook' => 1
        ));

        $books->first()->remove();

        $this->assertEquals(Book::searcher()->all()->count(), 9);
    }

}
