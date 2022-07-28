<?php

namespace App\PhpTests;

use KanbanBoard\Authentication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

final class AuthenticationTest extends TestCase
{

    public $envFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->envFilePath = __DIR__.'/../.env';

        $dotenv = new Dotenv();
        $dotenv->load($this->envFilePath);
    }

}