<?php

namespace Src\Module\ElasticSearch\Search\Post\Enum;

enum MetaCompare: string {
    case EQUAL = '=';
    case NOT_EQUAL = '!=';
    case EXISTS = 'EXISTS';
    case NOT_EXISTS = 'NOT_EXISTS';
    case IN = 'IN';
    case NOT_IN = 'NOT_IN';

    public static function createByValue(string $value): MetaCompare {
        return match (strtoupper($value)){
            '=' => MetaCompare::EQUAL,
            '!=' => MetaCompare::NOT_EQUAL,
            'EXISTS' => MetaCompare::EXISTS,
            'NOT_EXISTS' => MetaCompare::NOT_EXISTS,
            'NOT_IN' => MetaCompare::NOT_IN,
            default => MetaCompare::IN,
        };
    }
}