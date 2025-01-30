<?php

namespace Src\Module\ElasticSearch\DTO\Transformer;

use Src\Core\Entity\Term;
use Src\Core\Repository\TermTaxonomyRepository;
use Src\Core\Transformer\TransformerAbstract;
use Src\Module\ElasticSearch\DTO\Model\TermDTO;

class TermDTOFromEntityTransformer extends TransformerAbstract
{
    public function __construct(
        private readonly TermTaxonomyRepository $termTaxonomyRepository,
    ) {
    }

    /**
     * @param Term $data
     * @param array $context
     *
     * @return TermDTO
     */
    public function transform($data, array $context = []): TermDTO
    {

        $model       = new TermDTO();
        $model->id   = $data->getId();
        $model->name = $data->getName();
        $model->slug = $data->getSlug();

        $taxonomy = $this->termTaxonomyRepository->findOneBy([
            'termId' => $data->getId()
        ]);

        $model->taxonomyName = $taxonomy->getTaxonomy();

        return $model;
    }

    static function supportsTransformation($data, string $to = null, array $context = []): bool
    {
        return $data instanceof Term && TermDTO::class === $to;
    }
}
