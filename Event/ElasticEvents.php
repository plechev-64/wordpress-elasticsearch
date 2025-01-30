<?php

namespace Src\Module\ElasticSearch\Event;

class ElasticEvents
{
    public const PreUpdatePostIndex = 'PreUpdatePostIndex';
    public const PreSearchPostIndex = 'PreSearchPostIndex';
    public const PreSearchWPQuery = 'PreSearchWPQuery';
}