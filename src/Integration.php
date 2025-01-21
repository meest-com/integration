<?php

namespace Meest\Integration;

use Exception;

class Integration
{
    const CACHE_TOKEN_KAY = 'meest_integration';

    private $config = [
        'cache_dir' => null,
        'url' => 'https://integration.meest.com',
        'login' => null,
        'password' => null,
        'token' => null,
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function tokenGet(): array
    {
        if (empty($this->config['login']) || empty($this->config['password'])) {
            throw new Exception('The credentials are wrong');
        }

        return $this->request('POST', '/oauth2/token', [
            'login' => $this->config['login'],
            'password' => $this->config['password'],
        ], false);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function userCreate(array $data): array
    {
        return $this->request('POST', '/user', $data);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function userAddressCreate(array $data): array
    {
        return $this->request('POST', '/user-address', $data);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function shipmentCreate(array $data): array
    {
        return $this->request('POST', '/shipment', $data);
    }

    /**
     * @param int $shipmentId
     * @return array
     * @throws Exception
     */
    public function labelGet(int $shipmentId)
    {
        return $this->request('GET', "/label?shipmentId=$shipmentId", [], true, 'file');
    }

    /**
     * @param string $method
     * @param string $action
     * @param array $params
     * @param bool $isAuth
     * @param string $type
     * @return array|string
     * @throws Exception
     */
    public function request(string $method, string $action, array $params = [], bool $isAuth = true, string $type = 'json')
    {
        $headers = ['Content-Type: application/json'];

        $curl = curl_init($this->config['url'] . $action);

        if ($isAuth) {
            $this->tokenCheck();
            $headers[] = "Authorization: Bearer {$this->config['token']}";
        }

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($params)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        switch ($statusCode) {
            case 401:
                throw new Exception('Unauthorized');
            case 200:
                if ($type === 'json') {
                    return json_decode($response, true);
                }

                return $response;
            default:
                $response = json_decode($response, true);

                throw new Exception($response['message']);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function tokenCheck()
    {
        $cache = $this->tokenCache(self::CACHE_TOKEN_KAY);
        if ($cache !== false) {
            $this->config['token'] = $cache;
            $tokenArray = explode('.', $this->config['token']);
            $payload = json_decode(base64_decode($tokenArray[1]), true);
            if (($payload['exp'] - 10) > date('U')) {
                return;
            }
        }

        $token = $this->tokenGet();
        $this->tokenCache(self::CACHE_TOKEN_KAY, $token['accessToken']['tokenValue']);
        $this->config['token'] = $token['accessToken']['tokenValue'];
    }

    /**
     * @param string $key
     * @param $value
     * @return bool|string
     * @throws Exception
     */
    private function tokenCache(string $key, $value = null)
    {
        $file = $this->config['cache_dir'] . "$key.json";

        if ($value === null) {
            if (!file_exists($file)) {
                return false;
            }
            $content = file_get_contents($file);
            if ($content !== false) {
                return $content;
            }
        } else {
            $content = file_put_contents($file, $value);
            if ($content !== false) {
                return true;
            }
        }

        throw new Exception('Cache error');
    }
}
