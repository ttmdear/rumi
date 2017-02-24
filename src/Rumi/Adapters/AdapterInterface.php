<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\Adapters;

use Rumi\Orm\RecordInterface;

interface AdapterInterface
{
    /**
     * Wywołuje dowolne polecenie.
     *
     * @param string $command
     * @param array  $params
     *
     * @return boolen
     */
    public function execute($command, $params = array());

    /**
     * Wykonuje polecenie i zwraca dane.
     *
     * @param string $command
     * @param array  $params
     *
     * @return array
     */
    public function fetch($command, $params = array());

    /**
     * Returns object of searcher.
     *
     * @param  string $searcherClass Klasa obslugujaca wyszukiwanie wierszy.
     * Jeśli wartość nie zostanie podana, to zostanie wykorzystana klasa
     * adaptera.
     * @return \Rumi\Orm\SearcherInterface
     */
    public function searcher($searcherClass = null);

    /**
     * Tworzy nowe rekord w danych.
     *
     * @param  string $target
     * $param array $data
     * @return self
     */
    public function create($target, $data);

    /**
     * Usuwam record z danych.
     *
     * @param  string $target
     * @param  array  $id
     * @return self
     */
    public function remove($target, $id);

    /**
     * Aktualizuje podany record.
     *
     * @param  string $target
     * $param array $id
     * @return self
     */
    public function update($target, $id, $data);

    /**
     * Rejestruje wykorzystanie rekordu przez kolekcje.
     *
     * @param  string                    $target
     * @param  array                     $id
     * @param  \Rumi\Orm\RecordInterface $record
     * @param  string                    $collectionId
     * @return self
     */
    public function registerRecord($target, $id, RecordInterface $record, $collectionId = null);

    /**
     * Wyszukiuje rekord wśród zarejestrowanych.
     *
     * @param  string $target
     * @param  array  $id
     * @return null|RecordInterface
     */
    public function findRecord($target, $id);

    /**
     * Usuwa kolekcje z rejestru rekordów.
     *
     * @return self
     */
    public function unregisterUse($collectionId);
}
