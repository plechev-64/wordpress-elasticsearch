<?php

namespace Src\Module\ElasticSearch\DTO\Transformer;

use Src\Core\Transformer\TransformerAbstract;
use Src\Module\ElasticSearch\DTO\Model\PostDTO;
use Src\Module\ElasticSearch\DTO\Model\PostMetaDTO;
use Src\Module\ElasticSearch\DTO\Model\TermDTO;
use Src\Module\ElasticSearch\Search\SearchResponseHit;

class PostDTOFromSearchResponseTransformer extends TransformerAbstract
{

    /**
     * @param SearchResponseHit $data
     * @param array $context
     *
     * @return PostDTO
     */
    public function transform($data, array $context = []): PostDTO
    {

        $postData = $data->data;

        $model                = new PostDTO();
        $model->id            = (int)$postData['id'];
        $model->postType      = $postData['postType'];
        $model->postStatus    = $postData['postStatus'];
        $model->commentCount  = (int)$postData['commentCount'];
        $model->commentStatus = $postData['commentStatus'];
        $model->menuOrder     = (int)$postData['menuOrder'];
        $model->postAuthor    = (int)$postData['postAuthor'];
        $model->postContent   = $postData['postContent'];
        $model->postDate      = (int)$postData['postDate'];
        $model->postExcerpt   = $postData['postExcerpt'];
        $model->postMimeType  = $postData['postMimeType'];
        $model->postName      = $postData['postName'];
        $model->postParent    = (int)$postData['postParent'];
        $model->postTitle     = $postData['postTitle'];

        if ($postData['meta']) {
            foreach ($postData['meta'] as $key => $value) {
                $meta          = new PostMetaDTO();
                $meta->key     = $key;
                $meta->value   = stripslashes($value);
                $model->meta[] = $meta;
            }
        }

        if ($postData['terms']) {
            foreach ($postData['terms'] as $taxonomyName => $terms) {
                foreach ($terms as $termData) {
                    $term               = new TermDTO();
                    $term->id           = $termData['id'];
                    $term->slug         = $termData['slug'];
                    $term->name         = $termData['name'];
                    $term->taxonomyName = $taxonomyName;
                    $model->terms[]     = $term;
                }
            }
        }

        return $model;
    }

    static function supportsTransformation($data, string $to = null, array $context = []): bool
    {
        return $data instanceof SearchResponseHit && PostDTO::class === $to;
    }
}
