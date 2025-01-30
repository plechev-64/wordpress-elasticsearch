<?php

namespace Src\Module\ElasticSearch;

use Doctrine\Common\EventManager;
use Src\Core\Entity\Post;
use Src\Core\Repository\PostRepository;
use Src\Core\Transformer\TransformerManager;
use Src\Core\Wordpress\PostType\PostTypeManager;
use Src\Module\ElasticSearch\DTO\Model\PostDTO;
use Src\Module\ElasticSearch\Enum\Index;
use Src\Module\ElasticSearch\Event\ElasticEvents;
use Src\Module\ElasticSearch\Event\EventMessage\PreSearchPostIndexMessage;
use Src\Module\ElasticSearch\Event\EventMessage\PreUpdatePostIndexMessage;
use Src\Module\ElasticSearch\Index\PostIndex;
use Src\Module\ElasticSearch\Search\Post\PostRequest;
use Src\Module\ElasticSearch\Search\Post\PostSearchFilter;
use Src\Module\ElasticSearch\Search\SearchResponseHit;
use Src\Module\ElasticSearch\Search\SearchResult;
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