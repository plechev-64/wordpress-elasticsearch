<?php

namespace Src\Module\ElasticSearch\Index;

use Src\Module\ElasticSearch\DTO\Model\PostDTO;
use Src\Module\ElasticSearch\Enum\Index;

class PostIndex extends ElasticIndex
{
    public PostDTO $body;

    public function __construct(PostDTO $post)
    {
        parent::__construct($post->id, Index::POSTS->value);
        $this->body = $post;
    }
}