<?php

namespace ApiConnect\connectors;

use ApiConnect\connectors\interfaces\ApiCallInterface;
use ApiConnect\helpers\UrlCaller;

class ApiCall implements ApiCallInterface
{
    private string $apiTokenUrl;
    private string $apiDataUrl;
    private array $tokenParams;
    private UrlCaller $urlCaller;

    /**
     * ApiCall constructor.
     * @throws \RuntimeException
     */
    public function __construct()
    {
        $this->apiTokenUrl = $this->getRequiredEnvParam("API_TOKEN_URL");
        $this->apiDataUrl = $this->getRequiredEnvParam("API_DATA_URL");

        $this->tokenParams = [
            'client_id' => $this->getRequiredEnvParam("API_CLIENT_ID"),
            'email' => $this->getRequiredEnvParam("API_CLIENT_EMAIL"),
            'name' => $this->getRequiredEnvParam("API_CLIENT_NAME"),
        ];

        $this->urlCaller = new UrlCaller();
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        $tokenEncodedString = $this->urlCaller->call($this->apiTokenUrl, [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->tokenParams
        ]);

        if (empty($tokenEncodedString) || $this->urlCaller->errno) {
            throw new \RuntimeException('API Access Token is not generated', $this->urlCaller->errno);
        }

        $tokenArray = \json_decode($tokenEncodedString, true);

        if (empty($tokenArray['data']['sl_token']) || !$this->validateToken($tokenArray['data'])) {
            throw new \RuntimeException('API Access Token is not valid', $this->urlCaller->errno);
        }

        return $tokenArray['data']['sl_token'];
    }

    /**
     * @param string $token
     * @param int $page
     * @return array
     */
    public function getData(string $token, int $page = 1): array
    {
        $url = "{$this->apiDataUrl}?sl_token={$token}&page={$page}&max_posts=1000";
        $data = $this->urlCaller->call($url);

        if (!$data || $this->urlCaller->errno) {
            throw new \RuntimeException('No data returned', $this->urlCaller->errno);
        }

        return \json_decode($data, true);
    }

    /**
     * @param string $paramName
     * @return string
     * @throws \RuntimeException
     */
    public function getRequiredEnvParam(string $paramName): string
    {
        $value = getenv($paramName);

        if (!$value) {
            throw new \RuntimeException(sprintf('%s is not found in .env', $paramName));
        }

        return $value;
    }

    /**
     * @param array $tokenData
     * @return bool
     */
    private function validateToken(array $tokenData): bool
    {
        if (
            !empty($tokenData['email'])
            && !empty($tokenData['client_id'])
            && $tokenData['email'] === $this->tokenParams['email']
            && $tokenData['client_id'] === $this->tokenParams['client_id']
        ) {
            return true;
        }

        return false;
    }
}