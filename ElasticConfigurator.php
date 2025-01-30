<?php

namespace Src\Module\ElasticSearch;

use Src\Core\Container\ContainerBuilder;
use Src\Module\ElasticSearch\Command\UpdatePostsIndexCommand;
use Src\Module\ElasticSearch\DTO\Model\PostDTO;
use Src\Module\ElasticSearch\DTO\Model\PostMetaDTO;
use Src\Module\ElasticSearch\DTO\Model\TermDTO;
use Src\Module\ElasticSearch\DTO\Transformer\PostDTOFromEntityTransformer;
use Src\Module\ElasticSearch\DTO\Transformer\PostDTOFromSearchResponseTransformer;
use Src\Module\ElasticSearch\DTO\Transformer\PostMetaDTOFromEntityTransformer;
use Src\Module\ElasticSearch\DTO\Transformer\PostSearchFilterDTOFromWPQueryVarsTransformer;
use Src\Module\ElasticSearch\DTO\Transformer\TermDTOFromEntityTransformer;
use Src\Module\ElasticSearch\Search\Post\PostSearchFilter;

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