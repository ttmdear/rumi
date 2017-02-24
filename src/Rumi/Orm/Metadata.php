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

class Metadata
{
    private $metadata;
    private $definition;

    function __construct($recordClass, $definitionClass, $metadata)
    {
        $this->metadata = $metadata;

        if (!array_key_exists('definition', $metadata)) {
            throw new \Exception(printf("Plese define definition of columns at metadata of %s record.", $recordClass));
        }

        $this->definition = new $definitionClass($metadata['definition']);

        if (!array_key_exists('source', $metadata)) {
            throw new \Exception(printf("Plese define source for %s", $recordClass));
        }

        if (!array_key_exists('target', $metadata)) {
            throw new \Exception(printf("Please define target for %s record.", $recordClass));
        }
    }

    public function source()
    {
        return $this->metadata['source'];
    }

    public function target()
    {
        return $this->metadata['target'];
    }

    public function definition()
    {
        return $this->definition;
    }

    public function searcherClass()
    {
        if (!array_key_exists('searcherClass', $this->metadata)) {
            return null;
        }

        return $this->metadata['searcherClass'];
    }
}
