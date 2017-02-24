<?php
namespace Rumi\Tests\Cases;

use Rumi\Tests\Bookstore\Book;
use Rumi\Orm\SearcherInterface;
use Rumi\Orm\RecordInterface;
use Rumi\Orm\Collection;

class AggregatorTest extends \Rumi\Tests\TestCase
{
    public function testGroup()
    {
        // $books = Book::searcher()->all();

        // $aggregator = $books->group(function($record){
        //     $idBook = $record->get('idBook');

        //     $type = $idBook > 5 ? 'up' : 'down';

        //     return array(
        //         'idCategory' => $record->get('idCategory'),
        //         'type' => $type
        //     );
        // });


        // $ids = array();

        // // todo : delete
        // die(print_r(array_keys($aggregator->all()), true));
        // // endtodo
        // foreach ($aggregator as $collection) {
        //     foreach ($collection as $record) {
        //         if (!isset($ids[$i])) {

        //         }
        //     }
        // }
    }
}
