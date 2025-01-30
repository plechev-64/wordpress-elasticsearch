<?php

namespace Src\Module\ElasticSearch\Search\Post;

use Src\Module\ElasticSearch\Search\Filter\ElasticFilterAbstract;

class PostSearchFilter extends ElasticFilterAbstract
{
    /** @var int|array<int>|null */
    public null|int|array $id = null;
    /** @var int|array<int>|null */
    public null|int|array $notId = null;
    /** @var int|array<int>|null */
    public null|int|array $postAuthor = null;
    /** @var int|array<int>|null */
    public null|int|array $notPostAuthor = null;
    /** @var string|array<string>|null */
    public null|string|array $postStatus = 'publish';
    /** @var string|array<string>|null */
    public null|string|array $postType = null;
    /** @var int|array<int>|null */
    public null|int|array $postParent = null;
    /** @var string|array<string>|null */
    public null|string|array $postName = null;
    public ?PostMetaQuery $metaQuery = null;
    public ?PostTermQuery $termQuery = null;
    public ?PostDateQuery $dateQuery = null;
}