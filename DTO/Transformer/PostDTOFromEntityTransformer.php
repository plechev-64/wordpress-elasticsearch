<?php

namespace Src\Module\ElasticSearch\DTO\Transformer;

use Src\Core\Entity\Post;
use Src\Core\Repository\PostMetaRepository;
use Src\Core\Repository\TermRepository;
use Src\Core\Transformer\Exception\TransformerException;
use Src\Core\Transformer\TransformerAbstract;
use Src\Core\Transformer\TransformerManager;
use Src\Module\ElasticSearch\DTO\Model\PostDTO;
use Src\Module\ElasticSearch\DTO\Model\PostMetaDTO;
use Src\Module\ElasticSearch\DTO\Model\TermDTO;

class PostDTOFromEntityTransformer extends TransformerAbstract
{
    public function __construct(
        private readonly TermRepository $termRepository,
        private readonly PostMetaRepository $metaRepository,
        private readonly TransformerManager $transformerManager
    ) {
    }

    /**
     * @param Post $data
     * @param array $context
     *
     * @return PostDTO
     * @throws TransformerException
     */
    public function transform($data, array $context = []): PostDTO
    {

        $model                = new PostDTO();
        $model->id            = $data->getId();
        $model->postType      = $data->getPostType();
        $model->postStatus    = $data->getPostStatus();
        $model->commentCount  = $data->getCommentCount();
        $model->commentStatus = $data->getCommentStatus();
        $model->menuOrder     = $data->getMenuOrder()?? 0;
        $model->postAuthor    = $data->getPostAuthor();
        $model->postContent   = $data->getPostContent();
        $model->postDate      = $data->getPostDate() ? $data->getPostDate()->getTimestamp() : 0;
        $model->postExcerpt   = $data->getPostExcerpt()?? '';
        $model->postMimeType  = $data->getPostMimeType()?? '';
        $model->postName      = $data->getPostName()?? '';
        $model->postParent    = $data->getPostParent()?? 0;
        $model->postTitle     = $data->getPostTitle();

        $terms = $this->termRepository->findByObjectId($data->getId());
        if ($terms) {
            $dtoArray = $this->transformerManager->transformArray($terms, TermDTO::class);
            /** @var TermDTO $termDTO */
            foreach($dtoArray as $termDTO){
                $model->terms[$termDTO->taxonomyName][] = $termDTO;
            }
        }

        $meta = $this->metaRepository->findMetaByPostId($data->getId());

        if ($meta) {
            $dtoArray = $this->transformerManager->transformArray($meta, PostMetaDTO::class);
            /** @var PostMetaDTO $metaDTO */
            foreach($dtoArray as $metaDTO){
                $model->meta[$metaDTO->key] = $metaDTO->value;
            }
        }

        return $model;
    }

    static function supportsTransformation($data, string $to = null, array $context = []): bool
    {
        return $data instanceof Post && PostDTO::class === $to;
    }
}
