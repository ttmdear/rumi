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

use Rumi\Adapters\AdapterInterface;

interface RecordInterface
{
    const STATE_NEW = 1;
    const STATE_REMOVED = 2;
    const STATE_MODYFIED = 3;
    const STATE_SYNCED = 4;

    /**
     * Zapisuje dane w rekordzie. Jeśli record jest nowy, to zostanie wykonane
     * polecenie tworzenia rekordu.
     *
     * @return self
     */
    public function save();

    public static function searcher();

    public function adapter($adapter = null);
    public function target($target = null);
    public function definition($definition = null);
    public function set($name, $value);
    public function compareTo(\Rumi\Orm\RecordInterface $record, $column = null);
    public function get($column);
    public function defined($column);
    public function unsetColumn($column);

    /**
     * Zwraca wszystkie dane z recordu.
     *
     * @param  boolen $definition Ustawienie na true, powoduje zwrócenie tylko
     * danych zdefiniowanych w definicji rekordu.
     * @return array
     */
    public function data($definition = false);
    public function id();
    public function remove();
    public function reload();
    public function load($data);
    public function state($state = null);
    public function stateIs($state);
    public function toArray();
    public function jsonSerialize();
    public function toJson();
    public function toSql();
}
