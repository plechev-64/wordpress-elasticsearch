<?php

namespace Gosweb\Module\ElasticSearch\Event\EventMessage;

use Doctrine\Common\EventArgs;
use Gosweb\Module\ElasticSearch\Index\PostIndex;

class PreUpdatePostIndexMessage extends EventArgs
{
    private PostIndex $index;

    /**
     * @param PostIndex $index
     */
    public function __construct(PostIndex $index)
    {
        $this->index = $index;
    }

    /**
     * @return PostIndex
     */
    public function getIndex(): PostIndex
    {
        return $this->index;
    }

}