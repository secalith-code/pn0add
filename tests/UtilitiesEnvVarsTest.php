<?php

namespace App\PhpTests;

use App\Utilities;
use ArgumentCountError;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

final class UtilitiesEnvVarsTest extends TestCase
{

    public ?string $envFilePath;

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

    public function testArgumentCountError()
    {
        $this->expectException(ArgumentCountError::class);

        Utilities::env();
    }

    public function testNonExistentIndex()
    {
        $nonExistentIndex = 'NON_EXISTENT';

        $errMessageResult = Utilities::env($nonExistentIndex);

        $this->stringContains($nonExistentIndex);

    }
}