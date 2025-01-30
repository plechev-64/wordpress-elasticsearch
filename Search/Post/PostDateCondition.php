<?php

namespace Gosweb\Module\ElasticSearch\Search\Post;

class PostDateCondition
{
    public string $column;
    public string $after;
    public string $before;
    public bool $inclusive;

    /**
     * @param string $column
     * @param string $after
     * @param string $before
     * @param bool $inclusive
     */
    public function __construct(string $column, string $after, string $before, ?bool $inclusive = true)
    {
        $this->column    = $column;
        $this->after     = $after;
        $this->before    = $before;
        $this->inclusive = $inclusive;
    }

}