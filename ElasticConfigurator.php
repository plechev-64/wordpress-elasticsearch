<?php

namespace Gosweb\Module\ElasticSearch;

use Gosweb\Core\Container\ContainerBuilder;
use Gosweb\Module\ElasticSearch\Command\UpdatePostsIndexCommand;
use Gosweb\Module\ElasticSearch\DTO\Model\PostDTO;
use Gosweb\Module\ElasticSearch\DTO\Model\PostMetaDTO;
use Gosweb\Module\ElasticSearch\DTO\Model\TermDTO;
use Gosweb\Module\ElasticSearch\DTO\Transformer\PostDTOFromEntityTransformer;
use Gosweb\Module\ElasticSearch\DTO\Transformer\PostDTOFromSearchResponseTransformer;
use Gosweb\Module\ElasticSearch\DTO\Transformer\PostMetaDTOFromEntityTransformer;
use Gosweb\Module\ElasticSearch\DTO\Transformer\PostSearchFilterDTOFromWPQueryVarsTransformer;
use Gosweb\Module\ElasticSearch\DTO\Transformer\TermDTOFromEntityTransformer;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;

class ElasticConfigurator
{
    public static function configure(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions(
            include __DIR__ . '/config/definitions.php'
        );

        $containerBuilder->addController(ElasticController::class);

        $containerBuilder->addCommand(UpdatePostsIndexCommand::class);
        $containerBuilder->addTransformer(PostDTO::class, PostDTOFromEntityTransformer::class);
        $containerBuilder->addTransformer(PostDTO::class, PostDTOFromSearchResponseTransformer::class);
        $containerBuilder->addTransformer(TermDTO::class, TermDTOFromEntityTransformer::class);
        $containerBuilder->addTransformer(PostMetaDTO::class, PostMetaDTOFromEntityTransformer::class);
        $containerBuilder->addTransformer(PostSearchFilter::class, PostSearchFilterDTOFromWPQueryVarsTransformer::class);

        $containerBuilder->onWpReady(ElasticHooks::class);

    }
}