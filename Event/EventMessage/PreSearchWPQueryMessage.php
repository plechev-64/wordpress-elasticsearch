<?php

namespace Gosweb\Module\ElasticSearch\Event\EventMessage;

use Doctrine\Common\EventArgs;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;
use WP_Query;

class PreSearchWPQueryMessage extends EventArgs
{
    private PostSearchFilter $filter;
    private WP_Query $query;

    /**
     * @param PostSearchFilter $filter
     * @param WP_Query $query
     */
    public function __construct(PostSearchFilter $filter, WP_Query $query)
    {
        $this->filter = $filter;
        $this->query  = $query;
    }

    /**
     * @return PostSearchFilter
     */
    public function getFilter(): PostSearchFilter
    {
        return $this->filter;
    }

    /**
     * @return WP_Query
     */
    public function getQuery(): WP_Query
    {
        return $this->query;
    }
}