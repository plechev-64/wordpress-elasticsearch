<?php

namespace Gosweb\Module\ElasticSearch;

use Doctrine\Common\EventManager;
use Gosweb\Core\Entity\Post;
use Gosweb\Core\Repository\PostRepository;
use Gosweb\Core\Transformer\TransformerManager;
use Gosweb\Core\Wordpress\PostType\PostTypeManager;
use Gosweb\Module\ElasticSearch\DTO\Model\PostDTO;
use Gosweb\Module\ElasticSearch\Enum\Index;
use Gosweb\Module\ElasticSearch\Event\ElasticEvents;
use Gosweb\Module\ElasticSearch\Event\EventMessage\PreSearchPostIndexMessage;
use Gosweb\Module\ElasticSearch\Event\EventMessage\PreUpdatePostIndexMessage;
use Gosweb\Module\ElasticSearch\Index\PostIndex;
use Gosweb\Module\ElasticSearch\Search\Post\PostRequest;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;
use Gosweb\Module\ElasticSearch\Search\SearchResponseHit;
use Gosweb\Module\ElasticSearch\Search\SearchResult;
use Psr\Container\ContainerInterface;

class ElasticService
{

    private ?ElasticClient $client = null;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly TransformerManager $transformerManager,
        private readonly PostRepository $postRepository,
        private readonly PostTypeManager $postTypeManager,
        private readonly EventManager $eventManager
    ) {
    }

    /**
     * @return string[]
     */
    public function getSupportPostTypes(): array
    {
        return array_merge($this->postTypeManager->getPostTypes(), ['post', 'page']);
    }

    public function getClient(): ElasticClient
    {
        if ( ! $this->client) {
            $this->client = $this->container->get(ElasticClient::class);
        }

        return $this->client;
    }

    public function searchPosts(PostSearchFilter $filter): SearchResult
    {

        $this->eventManager->dispatchEvent(
            ElasticEvents::PreSearchPostIndex,
            new PreSearchPostIndexMessage($filter)
        );

        $response = $this->getClient()->search(
            new PostRequest($filter)
        );

        $responseArray = $response->asArray();

        $result = [];
        $total = 0;
        if ($responseArray['hits']) {
            $total = $responseArray['hits']['total']['value'];
            foreach ($responseArray['hits']['hits'] as $hit) {
                $result[] = $this->transformerManager->transform(new SearchResponseHit($hit['_source']),
                    PostDTO::class);
            }
        }

        return new SearchResult($total, $result);

    }

    public function updatePostIndexById(int $postId): void
    {
        /** @var Post $post */
        $post = $this->postRepository->find($postId);
        /** @var PostDTO $postIndex */
        $postDTO = $this->transformerManager->transform($post, PostDTO::class);

        $this->indexPost(
            new PostIndex($postDTO)
        );
    }

    public function deletePostIndexById(int $postId): void
    {
        $this->getClient()->delete($postId, Index::POSTS);
    }

    public function indexPost(PostIndex $index): void
    {

        $this->eventManager->dispatchEvent(
            ElasticEvents::PreUpdatePostIndex,
            new PreUpdatePostIndexMessage($index)
        );

        $this->getClient()->index($index);
    }
}