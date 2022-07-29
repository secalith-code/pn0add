<?php

use App\KanbanBoard\Authentication;
use App\Utilities;
use Clickalicious\Memcached\Client as CacheAdapter;
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
    'Memcached' => function (Container $c) {
        if(Utilities::env('MCACHED_ENABLED')) {
            $client = new CacheAdapter(
                Utilities::env('MCACHED_HOST'),
                (int) Utilities::env('MCACHED_PORT'),
                (int) Utilities::env('MCACHED_EXP')
            );

            return $client;
        }

        return false;
    }
];