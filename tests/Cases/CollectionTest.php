<?php
namespace Rumi\Tests\Cases;

use Rumi\Tests\Bookstore\Book;
use Rumi\Orm\SearcherInterface;
use Rumi\Orm\RecordInterface;
use Rumi\Orm\Collection;

class CollectionTest extends \Rumi\Tests\TestCase
{
    public function testIterate()
    {
        $this->reset();

        $index = array(
            '1' => 'name - 0',
            '2' => 'name - 1',
            '3' => 'name - 2',
            '4' => 'name - 3',
            '5' => 'name - 4',
            '6' => 'name - 5',
            '7' => 'name - 6',
            '8' => 'name - 7',
            '9' => 'name - 8',
            '10' => 'name - 9',
        );

        foreach (Book::searcher()->all() as $book) {
            $this->assertEquals($index[$book->get('idBook')], $book->get('name'));
        }
    }

    public function testCount()
    {
        $this->reset();

        $books = Book::searcher()->all();

        $this->assertEquals($books->count(), 10);
        $this->assertEquals(count($books), 10);

        $books = Book::searcher()
            ->id(array(
                'idBook' => 255
            ))
            ->all()
        ;

        $this->assertEquals($books->count(), 0);
        $this->assertEquals(count($books), 0);
    }

    public function testFirst()
    {
        $this->reset();

        $book = Book::searcher()->first();

        $this->assertInstanceOf(RecordInterface::class, $book);

        $book = Book::searcher()
            ->id(array(
                'idBook' => 255
            ))
            ->first()
        ;

        $this->assertEquals($book, null);
    }

    public function testSave()
    {
        $this->reset();

        // pobieram wszystkie rekordy
        $books = Book::searcher()->all();

        // zliczam ich ilosc
        $count = $books->count();

        // tworze index recordow aby moc potem odwolac sie do nich i sprawdzic
        // czy sa zgodne wartosci
        $index = array();

        // wprowadzam rozne zmiany
        foreach ($books as $book) {
            $index[$book->get('idBook')] = $book;

            $book->set('name', 'test - '.$book->get('idBook'));
            $book->set('price', rand(1,20));
            $book->set('releaseDate', $this->randomDate());
            $book->set('releaseDatetime', $this->randomDatetime());
            $book->set('idCategory', null);
        }

        // zapisuje cala kolekcje
        $books->save();

        // sprawdzam wynik zapisania
        $rows = Book::searcher()->fetch();

        $this->assertEquals(count($rows), $count);

        foreach ($rows as $row) {
            $this->assertArrayHasKey($row['idBook'], $index);

            $book = $index[$row['idBook']];

            $this->assertEquals($book->get('name'), $row['name']);
            $this->assertEquals($book->get('price'), $row['price']);
            $this->assertEquals($book->get('releaseDate'), $row['releaseDate']);
            $this->assertEquals($book->get('releaseDatetime'), $row['releaseDatetime']);
            $this->assertEquals($book->get('idCategory'), $row['idCategory']);

            unset($index[$row['idBook']]);
        }

        $this->assertEquals(count($index), 0);
    }

    public function testRemoveAndCreate()
    {
        $this->reset();

        // pobieram wszystkie rekordy
        $books = Book::searcher()->all();

        // tworze index tych recordow, oraz zliczam ich ilosc
        $count = $books->count();
        foreach ($books as $book) {
            $index[$book->get('idBook')] = $book;
        }

        // usuwam wszystkie recordy
        $books->remove();

        // powinno byc 0 po usunieciu
        $this->assertEquals(count(Book::searcher()->fetch()), 0);

        // zapisuje kolekcje, powinno to spowodowac utworzenie na nowo wierszy
        $books->save();

        // pobieram wszystkie wiersze
        $rows = Book::searcher()->fetch();

        // sprawdzam czy ilosc wierszy utworzynych jest poprawna
        $this->assertEquals(count($rows), $count);

        // przechodze po wszystkich wierszach
        foreach ($rows as $row) {
            $this->assertArrayHasKey($row['idBook'], $index);

            $book = $index[$row['idBook']];

            $this->assertEquals($book->get('idBook'), $row['idBook']);
            $this->assertEquals($book->get('name'), $row['name']);
            $this->assertEquals($book->get('price'), $row['price']);
            $this->assertEquals($book->get('releaseDate'), $row['releaseDate']);
            $this->assertEquals($book->get('releaseDatetime'), $row['releaseDatetime']);
            $this->assertEquals($book->get('idCategory'), $row['idCategory']);

            unset($index[$row['idBook']]);
        }

        $this->assertEquals(count($index), 0);
    }

    public function testInstanstable()
    {
        $this->reset();

        foreach (Book::searcher()->all() as $book) {
            $index[$book->get('idBook')] = $book;
        }

        foreach (Book::searcher()->all() as $book) {
            $this->assertArrayHasKey($book->get('idBook'), $index);
            $this->assertEquals($book, $index[$book->get('idBook')]);

            unset($index[$book->get('idBook')]);
        }

        $this->assertEquals(count($index), 0);
    }

    public function testCall()
    {
        $sum = 0;
        $books = Book::searcher()->all();
        $count = $books->count();
        $increaseBy = 10;

        foreach ($books as $book) {
            $sum += $book->get('price');
        }

        $books->call('increasePrice', array($increaseBy));
        $sumB = 0;

        foreach ($books as $book) {
            $sumB += $book->get('price');
        }

        $this->assertEquals(($sum + $count * $increaseBy), $sumB);
    }

    public function testFilter()
    {
        $books = Book::searcher()->all();

        $booksFiltered = $books->filter(function($record){

            if ($record->get('idBook') == '1') {
                return true;
            }

            if ($record->get('name') == 'name - 8') {
                return true;
            }

            return false;
        });

        $this->assertInstanceOf(Collection::class, $booksFiltered);
        $this->assertRows(array(1,9), $booksFiltered, 'books');
    }

    public function testSort()
    {
        $books = Book::searcher()->all();

        $sorted = $books->sort('asc');

        $index = array(
            '14.32',
            '17.96',
            '27.04',
            '30.55',
            '39.56',
            '43.43',
            '57.07',
            '86.57',
            '95.76',
            '99.19',
        );

        foreach ($sorted as $i => $record) {
            $this->assertEquals($index[$i], $record->get("price"));
        }

        $sorted = $books->sort('desc');

        $index = array(
            '99.19',
            '95.76',
            '86.57',
            '57.07',
            '43.43',
            '39.56',
            '30.55',
            '27.04',
            '17.96',
            '14.32',
        );

        foreach ($sorted as $i => $record) {
            $this->assertEquals($index[$i], $record->get("price"));
        }
    }

    public function testGroup()
    {
        $books = Book::searcher()->all();
        $aggregator = $books->group('idCategory');

        $i = 0;
        $ids = array(
            0 => array(1, 5, 6),
            1 => array(2, 8),
            2 => array(3),
            3 => array(4),
            4 => array(7, 10),
            5 => array(9),
        );

        foreach ($aggregator as $key => $collection) {
            $this->assertRows($ids[$i], $collection, 'books');
            $i++;
        }
    }
}
