<?php

namespace Gosweb\Module\ElasticSearch\Search;

use Gosweb\Module\ElasticSearch\Enum\Aggregator;
use Gosweb\Module\ElasticSearch\Enum\Operator;
use Gosweb\Module\ElasticSearch\Search\Filter\Condition;
use Gosweb\Module\ElasticSearch\Search\Filter\ConditionGroup;
use Gosweb\Module\ElasticSearch\Search\Filter\ElasticFilterAbstract;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;

abstract class ElasticRequest
{
    public string $index;
    public BodyRequest $body;

    public function __construct(string $index, ElasticFilterAbstract $filter)
    {
        $this->index = $index;
        $this->body  = $this->getBodyRequestByFilter($filter);
    }

    abstract function parseMatch(string $value, array &$conditions): void;

    abstract function fillFilterConditions(ElasticFilterAbstract $filter): void;

    private function getBodyRequestByFilter(ElasticFilterAbstract $filter): BodyRequest
    {

        $this->fillFilterConditions($filter);

        $conditions = [
            Aggregator::FILTER->value   => [],
            Aggregator::MUST->value     => [],
            Aggregator::MUST_NOT->value => [],
            Aggregator::SHOULD->value   => []
        ];

        if ($filter->match) {
            $this->parseMatch($filter->match, $conditions);
        }

        if ($conditionGroups = $filter->getConditionGroups()) {
            $this->parseConditionGroups($conditionGroups, $conditions);
        }

        $body = new BodyRequest(['bool' => $conditions]);

        if ($filter->offset) {
            $body->from = $filter->offset;
        }
        if ($filter->number) {
            $body->size = $filter->number;
        }
        if ($filter->sort) {
            $this->parseSort($filter->sort, $body);
        }

        return $body;

    }

    protected function parseConditionGroups(array $conditionGroups, array &$conditions): void
    {
        /** @var ConditionGroup $conditionGroup */
        foreach ($conditionGroups as $conditionGroup) {
            $this->parseConditionGroup($conditionGroup, $conditions);
        }
    }

    protected function parseConditionGroup(ConditionGroup $conditionGroup, array &$conditions): void
    {
        /** @var ConditionGroup|Condition $condition */
        foreach ($conditionGroup->conditions as $condition) {
            $groupConditions = [];
            if ($condition instanceof ConditionGroup) {
                $this->parseConditionGroup($condition, $groupConditions);
                $conditions[$conditionGroup->aggregator->value][] = [
                    'bool' => $groupConditions
                ];
            } elseif ($condition instanceof Condition) {
                $conditions[$conditionGroup->aggregator->value][] = $this->parseCondition($condition);
            }
        }
    }

    protected function parseCondition(Condition $condition): array
    {
        return match ($condition->operator) {
            Operator::IN => [
                'bool' => [
                    Aggregator::SHOULD->value => [
                        $this->getCondition('term', $condition->key, $condition->value)
                    ]
                ]
            ],
            Operator::NOT_IN,
            Operator::NOT_EQUAL => [
                'bool' => [
                    Aggregator::MUST_NOT->value => [

                        $this->getCondition('term', $condition->key, $condition->value)
                    ]
                ]
            ],
            Operator::EXISTS => [
                'bool' => [
                    Aggregator::MUST->value => array(
                        array(
                            'exists' => array(
                                'field' => $condition->key,
                            ),
                        ),
                    ),
                ]
            ],
            Operator::NOT_EXISTS => [
                'bool' => [
                    Aggregator::MUST_NOT->value => array(
                        array(
                            'exists' => array(
                                'field' => $condition->key,
                            ),
                        ),
                    ),
                ]
            ],
            Operator::GREATER => [
                'bool' => [
                    Aggregator::MUST->value => [
                        'range' => [
                            $condition->key => [
                                'gt' => $condition->value
                            ]
                        ]
                    ],
                ]
            ],
            Operator::GREATER_OR_EQUAL => [
                'bool' => [
                    Aggregator::MUST->value => [
                        'range' => [
                            $condition->key => [
                                'gte' => $condition->value
                            ]
                        ]
                    ],
                ]
            ],
            Operator::LESS => [
                'bool' => [
                    Aggregator::MUST->value => [
                        'range' => [
                            $condition->key => [
                                'lt' => $condition->value
                            ]
                        ]
                    ],
                ]
            ],
            Operator::LESS_OR_EQUAL => [
                'bool' => [
                    Aggregator::MUST->value => [
                        'range' => [
                            $condition->key => [
                                'lte' => $condition->value
                            ]
                        ]
                    ],
                ]
            ],
            /** EQUAL */
            default => [
                'bool' => [
                    Aggregator::MUST->value => [
                        $this->getCondition('term', $condition->key, $condition->value)
                    ]
                ]
            ],
        };

    }

    protected function parseSort(SortResult $sort, BodyRequest $body): void
    {

        /** @var SortCondition $condition */
        foreach ($sort->conditions as $condition) {
            $body->sort[] = [
                $condition->orderBy => [
                    'order' => $condition->order
                ]
            ];
        }

    }

    protected function parseFilterSearchParam(
        Operator $operator,
        string $name,
        string|int|array $value,
        PostSearchFilter $filter,
    ): void {

        $aggregators = [
            Aggregator::SHOULD,
            Aggregator::MUST
        ];
        if ($operator->isNegative()) {
            $aggregators = [
                Aggregator::MUST_NOT,
                Aggregator::MUST_NOT
            ];
        }

        $conditions = [];
        if (is_array($value)) {
            $aggregator = $aggregators[0];
            foreach ($value as $val) {
                $conditions[] = new Condition($operator, $name, $val);
            }
        } else {
            $aggregator   = $aggregators[1];
            $conditions[] = new Condition($operator, $name, $value);
        }

        $filter->addConditionGroup(new ConditionGroup(
            Aggregator::FILTER,
            new ConditionGroup(
                $aggregator,
                ...$conditions
            )
        ));

    }

    protected function getCondition(string $type, string $field, string|int|array $value): array
    {
        $conditions = [
            $type => []
        ];

        if (is_array($value)) {
            foreach ($value as $val) {
                $conditions[$type][$field] = $val;
            }
        } else {
            $conditions[$type][$field] = $value;
        }

        return $conditions;

    }
}