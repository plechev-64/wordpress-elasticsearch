<?php

namespace Src\Module\ElasticSearch\DTO\Transformer;

use Src\Core\Entity\PostMeta;
use Src\Core\Transformer\TransformerAbstract;
use Src\Module\ElasticSearch\DTO\Model\PostMetaDTO;

class PostMetaDTOFromEntityTransformer extends TransformerAbstract
{

    /**
     * @param PostMeta $data
     * @param array $context
     *
     * @return PostMetaDTO
     */
    public function transform($data, array $context = []): PostMetaDTO
    {

        $model        = new PostMetaDTO();
        $model->value = $data->getValue();
        $model->key   = $data->getKey();

        return $model;
    }

    static function supportsTransformation($data, string $to = null, array $context = []): bool
    {
        return $data instanceof PostMeta && PostMetaDTO::class === $to;
    }
}
