<?php

namespace Src\Module\ElasticSearch\Search\Post;

use Src\Module\ElasticSearch\Search\Post\Enum\QueryRelation;

class PostTermQuery
{
    public array $conditions = [];
    public QueryRelation $relation = QueryRelation::AND;

    /**
     * @param PostTermCondition ...$conditions
     */
    public function __construct(PostTermCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public function setRelation(QueryRelation $relation): PostTermQuery
    {
        $this->relation = $relation;
        return $this;
    }

}