<?php

namespace Src\Module\ElasticSearch\Search\Post\Enum;

enum QueryRelation: string {
    case AND = 'AND';
    case OR = 'OR';
}