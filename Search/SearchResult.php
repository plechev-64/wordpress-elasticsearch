<?php

namespace Src\Module\ElasticSearch\Search;

class SearchResult
{
    public int $total = 0;
    public array $result = [];

    /**
     * @param int $total
     * @param array $result
     */
    public function __construct(int $total, array $result)
    {
        $this->total  = $total;
        $this->result = $result;
    }

}