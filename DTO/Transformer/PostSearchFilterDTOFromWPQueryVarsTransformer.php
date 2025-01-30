<?php

namespace Gosweb\Module\ElasticSearch\DTO\Transformer;

use Gosweb\Core\Transformer\TransformerAbstract;
use Gosweb\Module\ElasticSearch\Enum\SortOrder;
use Gosweb\Module\ElasticSearch\Search\Post\Enum\MetaCompare;
use Gosweb\Module\ElasticSearch\Search\Post\Enum\TermOperator;
use Gosweb\Module\ElasticSearch\Search\Post\Enum\QueryRelation;
use Gosweb\Module\ElasticSearch\Search\Post\PostDateCondition;
use Gosweb\Module\ElasticSearch\Search\Post\PostDateQuery;
use Gosweb\Module\ElasticSearch\Search\Post\PostMetaCondition;
use Gosweb\Module\ElasticSearch\Search\Post\PostMetaQuery;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;
use Gosweb\Module\ElasticSearch\Search\Post\PostTermCondition;
use Gosweb\Module\ElasticSearch\Search\Post\PostTermQuery;
use Gosweb\Module\ElasticSearch\Search\SortCondition;
use Gosweb\Module\ElasticSearch\Search\SortResult;

class PostSearchFilterDTOFromWPQueryVarsTransformer extends TransformerAbstract
{

    /**
     * @param array $data
     * @param array $context
     *
     * @return PostSearchFilter
     */
    public function transform($data, array $context = []): PostSearchFilter
    {

        $model = new PostSearchFilter();

        if ( ! empty($data['s'])) {
            $model->match = $data['s'];
        }

        if ( ! empty($data['numberposts'])) {
            $model->number = $data['numberposts'];
        }

        if ( ! empty($data['posts_per_page'])) {
            $model->number = $data['posts_per_page'];
        }

        if ($model->number < 0) {
            $model->number = 9999;
        }

        if ( ! empty($data['paged']) && (int)$data['paged'] > 1) {
            $data['offset'] = ($data['paged'] - 1) * $model->number;
        }

        if ( ! empty($data['offset'])) {
            $model->offset = $data['offset'];
        }

        if ( ! empty($data['include'])) {
            $model->id = is_string($data['include']) ? array_map('trim',
                explode(',', $data['include'])) : $data['include'];
        }

        if ( ! empty($data['exclude'])) {
            $model->notId = is_string($data['exclude']) ? array_map('trim',
                explode(',', $data['exclude'])) : $data['exclude'];
        }

        if ( ! empty($data['tax_query'])) {
            $model->termQuery = $this->parseTermQuery($data['tax_query']);
        }

        if ( ! empty($data['meta_query'])) {
            $model->metaQuery = $this->parseMetaQuery($data['meta_query']);
        }

        if ( ! empty($data['date_query'])) {
            $model->dateQuery = $this->parseDateQuery($data['date_query']);
        }

        if ( ! empty($data['post_type'])) {
            $model->postType = $data['post_type'] === 'any' ? null : $data['post_type'];
        }

        if ( ! empty($data['post_status'])) {
            $model->postStatus = $data['post_status'] === 'any' ? null : $data['post_status'];
        }

        if ( ! empty($data['author'])) {
            $model->postAuthor = $data['author'];
        }

        if ( ! empty($data['author__in'])) {
            $model->postAuthor = $data['author__in'];
        }

        if ( ! empty($data['author__not_in'])) {
            $model->notPostAuthor = $data['author__not_in'];
        }

        if ( ! empty($data['post_parent'])) {
            $model->postParent = $data['post_parent'];
        }

        if ( ! empty($data['orderby'])) {
            $model->sort = $this->parseSort($data['orderby'], $data['order']);
        }

        return $model;
    }

    private function parseSort(string $orderBy, string $order): SortResult
    {

        $orderBy = match ($orderBy) {
            'author' => 'postAuthor',
            'ID' => 'id',
            'date' => 'postDate',
            'name' => 'postName',
            'status' => 'postStatus',
            'title' => 'postTitle',
            'type' => 'postType',
            default => '_score'
        };

        $order = ! empty($order) && SortOrder::ASC->value === strtoupper($order) ? SortOrder::ASC : SortOrder::DESC;

        return new SortResult(
            (new SortCondition())->create($orderBy, $order)
        );

    }

    private function parseDateQuery(array $dateQuery): PostDateQuery
    {
        $conditions = [];
        foreach ($dateQuery as $query) {
            $conditions[] = new PostDateCondition(
                $query['column'],
                $query['after'],
                $query['before'],
                $query['inclusive'] ?? null
            );
        }

        return new PostDateQuery(...$conditions);
    }

    private function parseTermQuery(array $termQuery): PostTermQuery
    {

        $relation   = QueryRelation::AND;
        $conditions = [];
        foreach ($termQuery as $k => $query) {
            if ($k === 'relation' && strtoupper($query) === 'OR') {
                $relation = QueryRelation::OR;
                continue;
            }

            $operator = TermOperator::IN;
            if ( ! empty($query['operator'])) {
                $operator = TermOperator::createByValue($query['operator']);
            }

            $conditions[] = (new PostTermCondition())->create(
                $query['taxonomy'],
                match($query['field']){
                    'term_id' => 'id',
                    default => $query['field']
                },
                $query['terms'],
                $operator
            );
        }

        return (new PostTermQuery(...$conditions))->setRelation($relation);

    }

    private function parseMetaQuery(array $metaQuery): PostMetaQuery
    {

        $relation   = QueryRelation::AND;
        $conditions = [];
        foreach ($metaQuery as $k => $query) {
            if ($k === 'relation' && strtoupper($query) === 'OR') {
                $relation = QueryRelation::OR;
                continue;
            }

            $compare = MetaCompare::IN;
            if ( ! empty($query['compare'])) {
                $compare = MetaCompare::createByValue($query['compare']);
            }

            $conditions[] = (new PostMetaCondition())->create(
                $query['key'],
                $query['value'],
                $compare
            );
        }

        return (new PostMetaQuery(...$conditions))->setRelation($relation);

    }

    static function supportsTransformation($data, string $to = null, array $context = []): bool
    {
        return is_array($data) && PostSearchFilter::class === $to;
    }
}
