<?php

namespace App\KanbanBoard\Authentication;

class JWTAdapterFactory extends AdapterFactory
{
    public function createAdapter($params)
    {
        $adapter = new AuthAdapter_Db();
        $adapter->doSomething();

        return $adapter;
    }
}
