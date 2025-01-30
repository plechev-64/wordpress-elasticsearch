<?php

namespace Gosweb\Module\ElasticSearch\Search\Post;

use Gosweb\Module\ElasticSearch\Search\Post\Enum\MetaCompare;

class PostMetaCondition
{
    public string $key;
    public string|array $value;
    public string $compare = '=';

    /**
     * @param string $key
     * @param array|string $value
     * @param MetaCompare|null $compare
     *
     * @return PostMetaCondition
     */
    public function create(string $key, array|string $value, ?MetaCompare $compare = MetaCompare::EQUAL): PostMetaCondition
    {
        $this->key   = $key;
        $this->value = $value;
        if ($compare) {
            $this->compare = $compare->value;
        }

        return $this;

    }


}