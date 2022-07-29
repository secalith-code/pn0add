<?php

use App\KanbanBoard\Authentication;
use App\Utilities;
use Clickalicious\Memcached\Client as MemcachedCacheAdapter;
use DI\Container;
use function DI\env as env;
use App\KanbanBoard\Domain\RepositoryModel;


return [
    'gh.client.id' => env('GH_CLIENT_ID'),
    'gh.client.secret' => env('GH_CLIENT_SECRET'),
    'mcached.status' => env('MCACHED_ENABLED'),
    'mcached.host' => env('MCACHED_HOST'),
    'mcached.port' => env('MCACHED_PORT'),
    'mcached.exp' => env('MCACHED_EXP'),
    'gh.repositories' => function () {
        return Utilities::getRepositoriesNames();
    },
    'Authentication' => function (Container $c) {
        return new Authentication(
            $c->get('gh.client.id'),
            $c->get('gh.client.secret'),
        );
    },
    'RepositoryModel' => DI\autowire(RepositoryModel::class)
        ->constructorParameter(
            'repositories',
            DI\get('gh.repositories')
        ),
    'MemcachedCacheAdapter' => DI\autowire(MemcachedCacheAdapter::class)
        ->constructorParameter('host', DI\get('mcached.host')
        )->constructorParameter('port', DI\get('mcached.port')
        )->constructorParameter('timeout',DI\get('mcached.exp')
        )
    ,
    'CacheAdapter' => function (Container $c) {
        if(Utilities::env('MCACHED_ENABLED')) {
            return $c->get('MemcachedCacheAdapter');
        }

        return false;
    }
];