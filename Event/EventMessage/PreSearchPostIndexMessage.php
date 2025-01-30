<?php

namespace Src\Module\ElasticSearch\Event\EventMessage;

use Doctrine\Common\EventArgs;
use Src\Module\ElasticSearch\Search\Post\PostSearchFilter;

class PreSearchPostIndexMessage extends EventArgs
{
    private PostSearchFilter $request;

    /**
     * @param PostSearchFilter $request
     */
    public function __construct(PostSearchFilter $request)
    {
        $this->request = $request;
    }

    /**
     * @return PostSearchFilter
     */
    public function getRequest(): PostSearchFilter
    {
        return $this->request;
    }
}