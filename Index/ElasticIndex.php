<?php

namespace Gosweb\Module\ElasticSearch\Index;

abstract class ElasticIndex
{
    public int $id;
    public string $index;

    /**
     * @param int $id
     * @param string $index
     */
    public function __construct(int $id, string $index)
    {
        $this->id    = $id;
        $this->index = $index;
    }

}