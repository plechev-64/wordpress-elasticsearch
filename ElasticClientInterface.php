<?php

namespace Src\Module\ElasticSearch;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Src\Module\ElasticSearch\Index\ElasticIndex;
use Src\Module\ElasticSearch\Search\ElasticRequest;
use Http\Promise\Promise;

interface ElasticClientInterface
{
    public function index(ElasticIndex $index): void;
    public function search(ElasticRequest $search): Elasticsearch;
}