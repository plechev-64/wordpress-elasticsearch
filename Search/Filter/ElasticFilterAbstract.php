<?php

namespace Gosweb\Module\ElasticSearch\Search\Filter;

use Gosweb\Module\ElasticSearch\Search\SortResult;

abstract class ElasticFilterAbstract
{
    /** @var string|null */
    public ?string $match = null;
    public ?int $number = 20;
    public ?int $offset = 0;
    public ?SortResult $sort = null;
    private array $conditionGroups = [];

    public function addConditionGroup(ConditionGroup $group): void
    {
        $this->conditionGroups[] = $group;
    }

    /**
     * @return array
     */
    public function getConditionGroups(): array
    {
        return $this->conditionGroups;
    }
}