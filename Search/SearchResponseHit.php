<?php

namespace Gosweb\Module\ElasticSearch\Search;

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