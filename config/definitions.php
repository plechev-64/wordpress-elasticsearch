<?php

return [
    /*
     * ========================================
     * Конфиги
     * ========================================
     */

    'es.host'   => DI\value(defined('ES_HOST') ? ES_HOST : null),
    'es.username'   => DI\value(defined('ES_USERNAME') ? ES_USERNAME : null),
    'es.password'   => DI\value(defined('ES_PASSWORD') ? ES_PASSWORD : null),

    /*
     * ========================================
     * Сервисы
     * ========================================
     */

    \Elastic\Elasticsearch\Client::class => DI\factory(function (Psr\Container\ContainerInterface $container) {
        return Elastic\Elasticsearch\ClientBuilder::create()
            ->setHttpClient(new GuzzleHttp\Client(['verify' => false ]))
            ->setHosts([$container->get('es.host')])
            ->setBasicAuthentication($container->get('es.username'), $container->get('es.password'))
            ->build();
    }),

];
