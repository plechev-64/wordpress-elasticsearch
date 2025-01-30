<?php

namespace Gosweb\Module\ElasticSearch\Search\Filter;

use Gosweb\Module\ElasticSearch\Enum\Aggregator;

class ConditionGroup {

    public Aggregator $aggregator;
    public array $conditions = [];

    /**
     * @param Aggregator $aggregator
     * @param array $conditions
     */
    public function __construct(Aggregator $aggregator, ConditionGroup|Condition ...$conditions)
    {
        $this->aggregator = $aggregator;
        $this->conditions = $conditions;
    }


}