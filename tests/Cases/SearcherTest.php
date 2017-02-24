<?php
namespace Rumi\Tests\Cases;

use Rumi\Tests\Bookstore\Book;
use Rumi\Orm\SearcherInterface;

class SearcherTest extends \Rumi\Tests\TestCase
{
    public function testInstanceOf()
    {
        // sprawdzam czy searcher implementuje odpowiednia klase
        // SearcherInterface
        $this->assertInstanceOf(SearcherInterface::class, Book::searcher());
    }

    public function testFetch()
    {
        $this->reset();

        $books = Book::searcher();

        $rows = $books->fetch();

        $this->assertEquals(10, count($rows));

        $first = $rows[0];

        // sprawdzam czy zawiera minumum zdefiniowanych informacji
        $this->assertArrayHasKey('idBook', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('idCategory', $first);
        $this->assertArrayHasKey('releaseDate', $first);
        $this->assertArrayHasKey('releaseDatetime', $first);
        $this->assertArrayHasKey('price', $first);
    }

    public function testExtendedSearch()
    {
        $this->reset();

        // sprawdzam rozszerzanie zapytania dziala poprawnie

        $books = Book::searcher();

        $books
            ->leftJoin('books Categories Dic', 'a')
            ->using('idCategory')
            ->column('name')->alias('bookCategory')
            ->context()
            ->context()
            ->column('a.name')->isNotNull()
        ;

        $rows = $books->fetch();
        $categories = array();

        $this->assertEquals(count($rows), 7);

        foreach ($rows as $row) {
            if (!isset($categories[$row['bookCategory']])) {
                $categories[$row['bookCategory']] = 0;
            }

            $categories[$row['bookCategory']]++;
        }

        $this->assertEquals($categories['Science fiction'], 2);
        $this->assertEquals($categories['Drama'], 1);
        $this->assertEquals($categories['Journals'], 1);
        $this->assertEquals($categories['Horror'], 2);
        $this->assertEquals($categories['Religion, Spirituality & New Age'], 1);
    }

    public function testId()
    {
        $this->reset();

        $rows = Book::searcher()->id(array(
            'idBook' => 1
        ))->fetch();

        $this->assertEquals(count($rows), 1);

        $row = $rows[0];

        $this->assertEquals($row['name'], 'name - 0');
    }
}
