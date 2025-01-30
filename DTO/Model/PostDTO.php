<?php

namespace Src\Module\ElasticSearch\DTO\Model;

class PostDTO
{
    public int $id;
    public int $postAuthor;
    public string $postStatus;
    public string $postType;
    public int $postDate;
    public string $postTitle;
    public string $postContent;
    public string $postExcerpt;
    public int $postParent;
    public string $postName;
    public string $postMimeType;
    public int $menuOrder;
    public int $commentCount;
    public string $commentStatus;
    /** @var array<PostMetaDTO>  */
    public array $meta = [];
    /** @var array<TermDTO>  */
    public array $terms = [];
    public array $custom = [];
}