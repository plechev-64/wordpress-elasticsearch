<?php

namespace Src\Module\ElasticSearch\Event\EventMessage;

use Doctrine\Common\EventArgs;
use Src\Module\ElasticSearch\Index\PostIndex;

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