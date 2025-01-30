<?php

namespace Gosweb\Module\ElasticSearch;

use Doctrine\Common\EventManager;
use Gosweb\Core\Transformer\TransformerManager;
use Gosweb\Module\ElasticSearch\DTO\Model\PostDTO;
use Gosweb\Module\ElasticSearch\Event\ElasticEvents;
use Gosweb\Module\ElasticSearch\Event\EventMessage\PreSearchWPQueryMessage;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ElasticHooks
{

    private readonly ElasticService $elasticService;
    private readonly TransformerManager $transformerManager;
    private readonly EventManager $eventManager;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        ContainerInterface $container
    ) {
        $this->elasticService     = $container->get(ElasticService::class);
        $this->transformerManager = $container->get(TransformerManager::class);
        $this->eventManager       = $container->get(EventManager::class);
    }

    public function __invoke(): void
    {
        add_action('deleted_post', function (int $postId) {
            $this->elasticService->deletePostIndexById($postId);
        });

        add_action('wp_insert_post', function (int $postId, \WP_Post $post, bool $update) {
            $this->elasticService->updatePostIndexById($postId);
        }, 10, 3);

        add_filter('posts_pre_query', function (array|null $posts, \WP_Query $query) {
            if ($query->get('s') &&
                (is_array($query->get('post_type')) ||
                 is_string($query->get('post_type')) &&
                 in_array($query->get('post_type'), $this->elasticService->getSupportPostTypes()))
            ) {
                $posts = $this->findPostsByWPQuery($query);
            }

            return $posts;
        }, 10, 2);

    }

    private function findPostsByWPQuery(\WP_Query $query): ?array
    {

        /** @var PostSearchFilter $filter */
        $filter = $this->transformerManager->transform($query->query, PostSearchFilter::class);

        $this->eventManager->dispatchEvent(
            ElasticEvents::PreSearchWPQuery,
            new PreSearchWPQueryMessage($filter, $query)
        );

        $searchResult = $this->elasticService->searchPosts($filter);

        if ( ! $searchResult->total) {
            return null;
        }

        $query->found_posts = $searchResult->total;
        add_filter('found_posts', function (int $foundPosts) use ($searchResult) {
            return $searchResult->total;
        });

        $query->max_num_pages = ceil($query->found_posts / $filter->number);

        $ids = array_map(function (PostDTO $postDto) {
            return $postDto->id;
        }, $searchResult->result);

        return get_posts([
            'post_type'        => $filter->postType,
            'include'          => $ids,
            'suppress_filters' => false,
            'orderby'          => 'post__in',
            'no_found_rows'    => true
        ]);

    }
}