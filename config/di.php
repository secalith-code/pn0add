<?php

use App\KanbanBoard\Authentication;
use App\Utilities;
use DI\Container;
use Firebase\JWT\JWT;

return [
    'gh.client.id' => Utilities::env('GH_CLIENT_ID'),
    'gh.client.secret' => Utilities::env('GH_CLIENT_SECRET'),
    'gh.client.alg' => Utilities::env('GH_ALG'),
    'JWT' => new JWT(),
    'Authentication' => function (Container $c) {
        return new Authentication(
            $c->get('gh.client.id'),
            $c->get('gh.client.secret'),
            $c->get('gh.client.alg'),
            $c->get('JWT'),
        );
    },
];