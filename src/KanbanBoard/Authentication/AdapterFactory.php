<?php

namespace App\KanbanBoard\Authentication;

abstract class AdapterFactory
{
    protected static $_factory;

    public static function getFactory()
    {
        return self::$_factory;
    }

    public static function setFactory(AdapterFactory $factory)
    {
        self::$_factory = $factory;
    }

    abstract public function createAdapter($params);
}