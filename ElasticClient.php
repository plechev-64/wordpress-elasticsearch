<?php

namespace Gosweb\Module\ElasticSearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Gosweb\Module\ElasticSearch\Enum\Index;
use Gosweb\Module\ElasticSearch\Index\ElasticIndex;
use Gosweb\Module\ElasticSearch\Search\ElasticRequest;

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