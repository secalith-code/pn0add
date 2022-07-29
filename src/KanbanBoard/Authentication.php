<?php

namespace App\KanbanBoard;

use App\Utilities;
use Exception;

class Authentication
{
    /** @var string $clientId   Should be set in .env file as GH_CLIENT_ID */
    private string $clientId;

    /** @var string $clientSecret   Should be set in .env file as GH_CLIENT_SECRET */
    private string $clientSecret;

    /**
     * @param string      $clientId
     * @param string      $clientSecret
     * @param string|null $alg
     */
    public function __construct(
        string $clientId,
        string $clientSecret
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
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
        $url .= '?client_id=' . $this->clientId;
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
        $data = [
            'code' => $code,
            'state' => 'LKHYgbn776tgubkjhk',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
            ],
        ];

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
