<?php

namespace Gosweb\Module\ElasticSearch;

use Gosweb\Core\Rest\Abstract\AbstractController;
use Gosweb\Core\Rest\Attributes\MapRequest;
use Gosweb\Core\Rest\Attributes\Route;
use Gosweb\Module\ElasticSearch\Search\Post\PostSearchFilter;
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