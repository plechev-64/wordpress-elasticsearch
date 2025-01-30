<?php

namespace Src\Module\ElasticSearch\Search\Post;

use Src\Module\ElasticSearch\Search\Post\Enum\TermOperator;

class PostTermCondition
{
    public string $taxonomy;
    public string $field;
    public string|array $terms;
    public string $operator = 'IN';

    /**
     * @param string $taxonomy
     * @param string $field
     * @param array|string $terms
     * @param TermOperator|null $operator
     *
     * @return PostTermCondition
     */
    public function create(
        string $taxonomy,
        string $field,
        array|string $terms,
        ?TermOperator $operator = TermOperator::IN
    ): PostTermCondition {
        $this->taxonomy = $taxonomy;
        $this->field    = $field;
        $this->terms    = $terms;
        if ($operator) {
            $this->operator = $operator->value;
        }

        return $this;
    }

}