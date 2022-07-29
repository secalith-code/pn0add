<?php

use App\KanbanBoard\Authentication;
use App\Utilities;
use Clickalicious\Memcached\Client as CacheAdapter;
use DI\Container;
use Firebase\JWT\JWT;
use function DI\env as env;

return [
    'gh.client.id' => env('GH_CLIENT_ID'),
    'gh.client.secret' => env('GH_CLIENT_SECRET'),
    'gh.client.alg' => env('GH_ALG'),
    'gh.repositories' => function () {
        return Utilities::getRepositoriesNames();
    },
    'JWT' => new JWT(),
    'Authentication' => function (Container $c) {
        return new Authentication(
            $c->get('gh.client.id'),
            $c->get('gh.client.secret'),
            $c->get('gh.client.alg'),
            $c->get('JWT'),
        );
    },
    'Memcached' => function (Container $c) {
        $client = new CacheAdapter(
            Utilities::env('MCACHED_HOST'),
            (int) Utilities::env('MCACHED_PORT'),
            (int) Utilities::env('MCACHED_EXP')
        );

        return $client;
    }
];