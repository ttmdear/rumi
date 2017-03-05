<?php
namespace Rumi\Tests\Bookstore;

class Book extends \Rumi\Adapters\Mysql\Record
{
    protected static $metadata = array(
        'source' => 'bookstore',
        'target' => 'books',
        'definition' => array(
            'idBook' => array(
                'pk',
                'autoincrement'
            ),
            'name',
            'idCategory',
            'releaseDate',
            'releaseDatetime',
            'price',
        )
    );

    public function increasePrice($num)
    {
        $this->set('price', $this->get('price') + $num);
    }

    public function compareTo(\Rumi\Orm\RecordInterface $record, $column = null)
    {
        if (is_null($column)) {
            if ($this->get('price') > $record->get('price')) {
                return 1;
            }

            if ($this->get('price') < $record->get('price')) {
                return -1;
            }

            return 0;
        }
    }
}
