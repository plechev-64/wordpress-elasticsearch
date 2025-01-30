<?php

namespace Src\Module\ElasticSearch\Search;

class SearchResponseHit
{
    public array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

}