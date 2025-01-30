<?php

namespace Src\Module\ElasticSearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Src\Module\ElasticSearch\Enum\Index;
use Src\Module\ElasticSearch\Index\ElasticIndex;
use Src\Module\ElasticSearch\Search\ElasticRequest;

class ElasticClient implements ElasticClientInterface
{

    public function __construct(
        private readonly Client $client
    ) {
    }

    public function index(ElasticIndex $index): void
    {
        $this->client->index(
            json_decode(json_encode($index), true)
        );
    }

    public function delete(int $id, Index $index): void
    {
        $this->client->delete([
            'id'    => $id,
            'index' => $index->value
        ]);
    }

    public function search(ElasticRequest $search): Elasticsearch
    {
        return $this->client->search(
            json_decode(json_encode($search), true)
        );
    }
}