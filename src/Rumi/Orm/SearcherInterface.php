<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Orm;

use Rumi\Orm\Definition;

interface SearcherInterface
{
    /**
     * Ustawia klasę obsługującą record.
     *
     * @param  string $recordClass Gdy wartość jest niepodana, to zwraca
     * aktualnie ustawioną.
     * @return string|self
     */
    public function recordClass($recordClass = null);

    /**
     * Ustawia obiekt definicji pól recordu.
     *
     * @param  \Rumi\Orm\Definition $definition Gdy wartość jest niepodana, to zwraca
     * aktualnie ustawioną.
     * @return \Rumi\Orm\Definition|self
     */
    public function definition(Definition $definition = null);

    /**
     * Ustawia target danego serchera.
     *
     * @param  string $target Gdy wartość jest niepodana, to zwraca
     * aktualnie ustawioną.
     * @return string|self
     */
    public function target($target = null);

    /**
     * Ustawia sercher w taki stan, aby został zwrócony wiersz o podanym id.
     *
     * @param  array $id Klucz podstawowy dla wyszukiwanego wiersza.
     * @return self
     */
    public function id($id);

    /**
     * Zwraca kolekcje rekordów.
     *
     * @param  array $params Parametry wywołania polecenia.
     * @return \Rumi\Orm\Collection
     */
    public function all($params = array());

    /**
     * Zwraca pierwszy record z kolekcji lub null jeśli żaden rekord nie
     * spełnia warunków wyszukiwania.
     *
     * @param  array $params Parametry wywołania polecenia.
     * @return null|\Rumi\Orm\Record
     */
    public function first($params = array());

    /**
     * Zwraca dane.
     *
     * @param  array $params Parametry wywołania polecenia.
     * @return array
     */
    public function fetch($params = array());
}
