<?php

namespace Src\Module\ElasticSearch\Search;

class BodyRequest
{
    public array $query;
    public int $size = 20;
    public int $from = 0;
    public array $sort = [];

    /**
     * @param array $query
     */
    public function __construct(array $query)
    {
        $this->query = $query;
    }

}