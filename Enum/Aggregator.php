<?php

namespace Src\Module\ElasticSearch\Enum;

enum Aggregator: string {
    case MUST = 'must';
    case MUST_NOT = 'must_not';
    case SHOULD = 'should';
    case FILTER = 'filter';
}