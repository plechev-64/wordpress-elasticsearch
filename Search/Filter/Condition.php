<?php

namespace Gosweb\Module\ElasticSearch\Search\Filter;

use Gosweb\Module\ElasticSearch\Enum\Operator;

class Condition
{
    public Operator $operator;
    public string $key;
    public mixed $value;

    /**
     * @param Operator $operator
     * @param string $key
     * @param mixed $value
     */
    public function __construct(Operator $operator, string $key, mixed $value)
    {
        $this->operator = $operator;
        $this->key      = $key;
        $this->value    = $value;
    }


}