<?php

namespace Src\Module\ElasticSearch\Search;

class SortResult
{
    public array $conditions = [];

    /**
     * @param array $conditions
     */
    public function __construct(SortCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }


}