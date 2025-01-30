<?php

namespace Gosweb\Module\ElasticSearch\Search\Post\Enum;

enum TermOperator: string {
    case AND = 'AND';
    case EXISTS = 'EXISTS';
    case NOT_EXISTS = 'NOT_EXISTS';
    case IN = 'IN';
    case NOT_IN = 'NOT_IN';

    public static function createByValue(string $value): TermOperator {
        return match (strtoupper($value)){
            'AND' => TermOperator::AND,
            'EXISTS' => TermOperator::EXISTS,
            'NOT_EXISTS' => TermOperator::NOT_EXISTS,
            'NOT_IN' => TermOperator::NOT_IN,
            default => TermOperator::IN,
        };
    }
}