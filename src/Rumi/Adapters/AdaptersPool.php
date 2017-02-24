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

class AdaptersPool
{
    private $adapters = array();
    private $configs;

    function __construct($configs)
    {
        foreach ($configs as $config) {
            if (!array_key_exists('name', $config)) {
                throw new \Exception("Name for adapter is not defined.");
            }

            $this->configs[$config['name']] = $config;
        }
    }

    public function get($name)
    {
        if (array_key_exists($name, $this->adapters)) {
            return $this->adapters[$name];
        }

        if (!array_key_exists($name, $this->configs)) {
            throw new \Exception("There is no configuration for {$name} adapter.");
        }

        $config = $this->configs[$name];

        $factory = new AdapterFactory();

        return $this->adapters[$name] = $factory->create($config);
    }
}
