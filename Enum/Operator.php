<?php

namespace Src\Module\ElasticSearch\Enum;

enum Operator: string {
    case EXISTS = 'EXISTS';
    case NOT_EXISTS = 'NOT_EXISTS';
    case IN = 'IN';
    case NOT_IN = 'NOT_IN';
    case EQUAL = '=';
    case NOT_EQUAL = '!=';
    case GREATER = '>';
    case GREATER_OR_EQUAL = '>=';
    case LESS = '<';
    case LESS_OR_EQUAL = '<=';

    public function isNegative(): bool {
        return match($this){
            self::NOT_EQUAL,
            self::NOT_EXISTS,
            self::NOT_IN => true,
            default => false,
        };
    }
}