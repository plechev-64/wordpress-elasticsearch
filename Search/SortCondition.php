<?php

namespace Gosweb\Module\ElasticSearch\Search;

use Gosweb\Module\ElasticSearch\Enum\SortOrder;

class SortCondition
{
    public string $orderBy;
    public string $order;

    /**
     * @param string $orderBy
     * @param SortOrder $order
     *
     * @return SortCondition
     */
    public function create(string $orderBy, SortOrder $order): SortCondition
    {
        $this->orderBy   = $orderBy;
        $this->order = $order->value;

        return $this;
    }

}