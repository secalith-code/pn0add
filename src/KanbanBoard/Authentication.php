<?php

namespace App\KanbanBoard;

use App\Utilities;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenSSLAsymmetricKey;

class Authentication
{

    private ?string $alg;

    private string $clientId;

    private string $clientSecret;

    private JWT $JWT;

    /**
     * @param string      $clientId
     * @param string      $clientSecret
     * @param string|null $alg
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        ?string $alg,
        ?JWT $jwt)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->alg = $alg;
        $this->JWT = $jwt;
    }

    /**
     * @return OpenSSLAsymmetricKey
     * @throws Exception
     */
    public function getPrivateKey(): OpenSSLAsymmetricKey
    {
        try {
            if (! file_exists(Utilities::env('GH_PEMKEY'))) {
                throw new Exception('your PEM file location is incorrect.');
            }

            return openssl_pkey_get_private(
                file_get_contents(Utilities::env('GH_PEMKEY')),
                Utilities::env('GH_PEMKEY_PASSPHRASE')
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getJWT(): string
    {
        return $this->JWT::encode(
            $this->getJWTPayload(),
            $this->getPrivateKey(),
            $this->alg
        );
    }

    /**
     * @return array
     */
    private function getJWTPayload(): array
    {
        return [
            'iss' => Utilities::env('GH_CLIENT_ID'),
            'iat' => time() - 10,
            'exp' => time() + (10 * 60)
        ];
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['gh-token']);
    }

    /**
     * @return mixed|string|void|null
     */
    public function login()
    {
        session_start();

        $token = null;

        if (array_key_exists('gh-token', $_SESSION)) {
            $token = $_SESSION['gh-token'];
        } elseif (
            Utilities::hasValue($_GET, 'code')
            && Utilities::hasValue($_GET, 'state')
            && $_SESSION['redirected']
        ) {
            $_SESSION['redirected'] = false;
            $token = $this->returnsFromGithub($_GET['code']);
        } else {
            $_SESSION['redirected'] = true;
            $this->redirectToGithub();
        }
        $this->logout();
        $_SESSION['gh-token'] = $token;
        return $token;
    }

    /**
     * @return void
     */
    private function redirectToGithub(): void
    {
        $url = 'Location: https://github.com/login/oauth/authorize';
        $url .= '?client_id=' . Utilities::env("GH_OAUTH_CLIENT_ID");
        $url .= '&scope=repo';
        $url .= '&state=LKHYgbn776tgubkjhk';
        header($url);
        exit();
    }

    /**
     * @param $code
     *
     * @return mixed|string|void|null
     */
    private function returnsFromGithub($code): mixed
    {
        $url = 'https://github.com/login/oauth/access_token';
        $data = array(
            'code' => $code,
            'state' => 'LKHYgbn776tgubkjhk',
            'client_id' => Utilities::env("GH_OAUTH_CLIENT_ID"),
            'client_secret' => Utilities::env("GH_OAUTH_CLIENT_SECRET"));
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            die('Error');
        }

        $result = explode('=', explode('&', $result)[0]);

        array_shift($result);

        return array_shift($result);
    }
}
