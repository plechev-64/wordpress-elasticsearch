<?php

namespace Gosweb\Module\ElasticSearch\Index;

use Gosweb\Module\ElasticSearch\DTO\Model\PostDTO;
use Gosweb\Module\ElasticSearch\Enum\Index;

class PostIndex extends ElasticIndex
{
    public PostDTO $body;

    public function __construct(PostDTO $post)
    {
        parent::__construct($post->id, Index::POSTS->value);
        $this->body = $post;
    }
}