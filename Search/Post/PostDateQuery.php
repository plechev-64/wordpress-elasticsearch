<?php

namespace Gosweb\Module\ElasticSearch\Search\Post;

class PostDateQuery
{
    /** @var PostDateCondition[] */
    public array $conditions = [];

    /**
     * @param PostDateCondition ...$conditions
     */
    public function __construct(PostDateCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }
}