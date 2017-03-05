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

class AdapterFactory
{
    public function create($config)
    {
        if (!array_key_exists('adapter', $config)) {
            throw new \Exception("The adapter is not defined.");
        }

        if (!array_key_exists('name', $config)) {
            throw new \Exception("Adapter should has defined name.");
        }

        $adapter = $config['adapter'];
        $name = $config['name'];

        switch ($adapter) {
        case 'mysql':
            return new \Rumi\Adapters\Mysql\Adapter($name, $config);
        case 'sqlite':
            return new \Rumi\Adapters\Sqlite\Adapter($name, $config);
        case 'pgsql':
            return new \Rumi\Adapters\Pgsql\Adapter($name, $config);
        default:
            throw new \Exception("Not supported adapter {$adapter}.");
            break;
        }
    }
}
