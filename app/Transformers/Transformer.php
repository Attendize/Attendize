<?php

namespace App\Transformers;

abstract class Transformer
{
    /**
     * @param $item
     * @return array
     */
    abstract public function transform($item);

    /**
     * @param array $items
     * @return array
     */
    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }
}
