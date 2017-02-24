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

    public static function searcher();
    public static function definitionClass();
    public static function metadata();

    public function set($name, $value);
    public function get($name);

    /**
     * Zwraca wszystkie dane z recordu.
     *
     * @param  boolen $definition Ustawienie na true, powoduje zwrócenie tylko
     * danych zdefiniowanych w definicji rekordu.
     * @return array
     */
    public function data($definition = false);
    public function load($data);

    /**
     * Zapisuje dane w rekordzie. Jeśli record jest nowy, to zostanie wykonane
     * polecenie tworzenia rekordu.
     *
     * @return self
     */
    public function save();

    public function remove();
    public function id();
    public function reload();
    public function adapter();
    public function target();
    public function definition();

    public function state($state = null);
    public function stateIs($state);
}
