<?php

namespace Gosweb\Module\ElasticSearch\Search\Post;

use Gosweb\Module\ElasticSearch\Enum\Aggregator;
use Gosweb\Module\ElasticSearch\Enum\Index;
use Gosweb\Module\ElasticSearch\Enum\Operator;
use Gosweb\Module\ElasticSearch\Search\ElasticRequest;
use Gosweb\Module\ElasticSearch\Search\Filter\Condition;
use Gosweb\Module\ElasticSearch\Search\Filter\ConditionGroup;
use Gosweb\Module\ElasticSearch\Search\Filter\ElasticFilterAbstract;
use Gosweb\Module\ElasticSearch\Search\Post\Enum\MetaCompare;
use Gosweb\Module\ElasticSearch\Search\Post\Enum\QueryRelation;
use Gosweb\Module\ElasticSearch\Search\Post\Enum\TermOperator;

class PostRequest extends ElasticRequest
{

    /**
     * @param PostSearchFilter $filter
     */
    public function __construct(PostSearchFilter $filter)
    {
        parent::__construct(Index::POSTS->value, $filter);
    }

    /**
     * @param PostSearchFilter $filter
     */
    public function fillFilterConditions(ElasticFilterAbstract $filter): void
    {
        if ($filter->id) {
            $this->parseFilterSearchParam(Operator::EQUAL, 'id', $filter->id, $filter);
        }

        if ($filter->notId) {
            $this->parseFilterSearchParam(Operator::NOT_EQUAL, 'id', $filter->notId, $filter);
        }

        if ($filter->postStatus) {
            $this->parseFilterSearchParam(Operator::EQUAL, 'postStatus', $filter->postStatus, $filter);
        }

        if ($filter->postType) {
            $this->parseFilterSearchParam(Operator::EQUAL, 'postType', $filter->postType, $filter);
        }

        if ($filter->postParent) {
            $this->parseFilterSearchParam(Operator::EQUAL, 'postParent', $filter->postParent, $filter);
        }

        if ($filter->postName) {
            $this->parseFilterSearchParam(Operator::EQUAL, 'postName', $filter->postName, $filter);
        }

        if ($filter->postAuthor) {
            $this->parseFilterSearchParam(Operator::EQUAL, 'postAuthor', $filter->postAuthor, $filter);
        }

        if ($filter->notPostAuthor) {
            $this->parseFilterSearchParam(Operator::NOT_EQUAL, 'postAuthor', $filter->notPostAuthor, $filter);
        }

        if ($filter->metaQuery?->conditions) {
            $this->parseMetaQuery($filter);
        }

        if ($filter->termQuery?->conditions) {
            $this->parseTermsQuery($filter);
        }

        if ($filter->dateQuery?->conditions) {
            $this->parseDateQuery($filter);
        }
    }

    public function parseMatch(string $value, array &$conditions): void
    {

        $searchFields = [
            'postTitle^3',
            'postContent^1',
        ];

        $conditions[Aggregator::MUST->value][] = [
            'bool' => [
                Aggregator::SHOULD->value => [
                    [
                        'multi_match' => [
                            'query'  => $value,
                            'type'   => 'phrase',
                            'fields' => $searchFields,
                            'boost'  => 1,
                        ],
                    ],
                    [
                        'multi_match' => [
                            'query'     => $value,
                            'fields'    => $searchFields,
                            'operator'  => 'and',
                            'boost'     => 1,
                            'fuzziness' => 'auto',
                        ],
                    ],
                    [
                        'multi_match' => [
                            'query'       => $value,
                            'type'        => 'cross_fields',
                            'fields'      => $searchFields,
                            'boost'       => 1,
                            'analyzer'    => 'standard',
                            'tie_breaker' => 0.5,
                            'operator'    => 'and',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function parseDateQuery(PostSearchFilter $filter): void
    {
        $metaQuery = $filter->dateQuery;
        foreach ($metaQuery->conditions as $condition) {
            $filter->addConditionGroup(new ConditionGroup(
                Aggregator::FILTER,
                new ConditionGroup(
                    Aggregator::MUST,
                    new Condition(
                        Operator::GREATER_OR_EQUAL, 'postDate',
                        (new \DateTime())
                            ->setTimestamp(strtotime($condition->after))
                            ->setTime(0, 0, 0)
                            ->getTimestamp()
                    ),
                    new Condition(
                        Operator::LESS_OR_EQUAL, 'postDate',
                        (new \DateTime())
                            ->setTimestamp(strtotime($condition->before))
                            ->setTime(23, 59, 59)
                            ->getTimestamp()
                    )
                )
            ));
        }
    }

    private function parseMetaQuery(PostSearchFilter $filter): void
    {

        $metaQuery = $filter->metaQuery;

        $metaAggregator = Aggregator::MUST;
        if ($metaQuery->relation === QueryRelation::OR->value) {
            $metaAggregator = Aggregator::SHOULD;
        }

        $conditions = [];
        foreach ($metaQuery->conditions as $condition) {

            $operator = match ($condition->compare) {
                MetaCompare::NOT_EQUAL->value => Operator::NOT_EQUAL,
                MetaCompare::EXISTS->value => Operator::EXISTS,
                MetaCompare::NOT_EXISTS->value => Operator::NOT_EXISTS,
                MetaCompare::EQUAL->value => Operator::EQUAL,
                default => Operator::IN,
            };

            $conditions[] = new Condition($operator, 'meta.' . $condition->key, $condition->value);

        }

        $filter->addConditionGroup(new ConditionGroup(
            Aggregator::FILTER,
            new ConditionGroup(
                $metaAggregator,
                ...$conditions
            )
        ));

    }

    private function parseTermsQuery(PostSearchFilter $filter): void
    {

        $metaQuery = $filter->termQuery;

        $metaAggregator = Aggregator::MUST;
        if ($metaQuery->relation === QueryRelation::OR) {
            $metaAggregator = Aggregator::SHOULD;
        }

        $conditions = [];
        foreach ($metaQuery->conditions as $condition) {

            $operator = match ($condition->compare) {
                TermOperator::EXISTS->value => Operator::EXISTS,
                TermOperator::NOT_EXISTS->value => Operator::NOT_EXISTS,
                TermOperator::AND->value => Operator::EQUAL,
                TermOperator::NOT_IN->value => Operator::NOT_IN,
                default => Operator::IN,
            };

            $conditions[] = new Condition($operator, 'terms.' . $condition->taxonomy . '.' . $condition->field,
                $condition->terms);

        }

        $filter->addConditionGroup(new ConditionGroup(
            Aggregator::FILTER,
            new ConditionGroup(
                $metaAggregator,
                ...$conditions
            )
        ));
    }

}