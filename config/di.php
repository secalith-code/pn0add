<?php

use App\KanbanBoard\Authentication;
use App\Utilities;
use Clickalicious\Memcached\Client as CacheAdapter;
use DI\Container;
use function DI\env as env;

return [
    'gh.client.id' => env('GH_CLIENT_ID'),
    'gh.client.secret' => env('GH_CLIENT_SECRET'),
    'gh.repositories' => function () {
        return Utilities::getRepositoriesNames();
    },
    'Authentication' => function (Container $c) {
        return new Authentication(
            $c->get('gh.client.id'),
            $c->get('gh.client.secret'),
        );
    },
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