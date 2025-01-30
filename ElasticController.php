<?php

namespace Src\Module\ElasticSearch;

use Src\Core\Rest\Abstract\AbstractController;
use Src\Core\Rest\Attributes\MapRequest;
use Src\Core\Rest\Attributes\Route;
use Src\Module\ElasticSearch\Search\Post\PostSearchFilter;
use Symfony\Component\HttpFoundation\Response;

#[Route(path: '/search')]
class ElasticController extends AbstractController
{

    #[Route(path: '/posts', method: 'GET', isFastApi: true)]
    public function searchPosts(
        #[MapRequest] PostSearchFilter $request,
        ElasticService $elasticService
    ): Response {
        return $this->success(
            $elasticService->searchPosts($request)
        );
    }
}