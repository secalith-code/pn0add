<?php

namespace App\PhpTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

final class EnvVarsTest extends TestCase
{

    public $envFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->envFilePath = __DIR__.'/../.env';

        $dotenv = new Dotenv();
        $dotenv->load($this->envFilePath);
    }

    public function testEnvFileExists()
    {
        $this->assertTrue(file_exists($this->envFilePath));
    }

    public function testEnvVarsExists()
    {
        $this->assertTrue(array_key_exists('GH_CLIENT_ID',$_SERVER));
        $this->assertTrue(array_key_exists('GH_CLIENT_SECRET',$_SERVER));
        $this->assertTrue(array_key_exists('GH_ACCOUNT',$_SERVER));
        $this->assertTrue(array_key_exists('GH_REPOSITORIES',$_SERVER));
    }

    public function testEnvVarsNotEmpty()
    {
        $this->assertTrue( ! empty($_SERVER['GH_CLIENT_ID']));
        $this->assertTrue( ! empty($_SERVER['GH_CLIENT_SECRET']));
        $this->assertTrue( ! empty($_SERVER['GH_ACCOUNT']));
        $this->assertTrue( ! empty($_SERVER['GH_REPOSITORIES']));
    }
}