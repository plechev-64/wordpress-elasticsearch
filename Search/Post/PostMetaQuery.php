<?php

namespace Gosweb\Module\ElasticSearch\Search\Post;

use Gosweb\Module\ElasticSearch\Search\Post\Enum\QueryRelation;

class PostMetaQuery
{
    /** @var PostMetaCondition[] */
    public array $conditions = [];
    public string $relation = 'AND';

    /**
     * @param PostMetaCondition ...$conditions
     */
    public function __construct(PostMetaCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public function setRelation(QueryRelation $relation): PostMetaQuery
    {
        $this->relation = $relation->value;

        return $this;
    }
}