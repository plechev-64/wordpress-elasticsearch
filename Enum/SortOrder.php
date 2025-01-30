<?php

namespace Src\Module\ElasticSearch\Enum;

enum SortOrder: string {
    case DESC = 'DESC';
    case ASC = 'ASC';
}