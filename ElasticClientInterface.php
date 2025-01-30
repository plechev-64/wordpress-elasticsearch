<?php

namespace Gosweb\Module\ElasticSearch;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Gosweb\Module\ElasticSearch\Index\ElasticIndex;
use Gosweb\Module\ElasticSearch\Search\ElasticRequest;
use Http\Promise\Promise;

interface ElasticClientInterface
{
    public function index(ElasticIndex $index): void;
    public function search(ElasticRequest $search): Elasticsearch;
}