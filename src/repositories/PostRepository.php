<?php

namespace ApiConnect\repositories;

use ApiConnect\connectors\interfaces\ApiCallInterface;
use ApiConnect\models\Post;

class PostRepository
{
    const TOKEN_EXPIRED_CODE = 401; // Unauthorized
    const MAX_LOAD_TRIES = 3;

    /**
     * @var ApiCallInterface
     */
    private ApiCallInterface $apiCaller;
    private ?string $token = null;

    /**
     * PostRepository constructor.
     * @param ApiCallInterface $apiCaller
     */
    public function __construct(ApiCallInterface $apiCaller)
    {
        $this->apiCaller = $apiCaller;
    }

    /**
     * @param int $page
     * @param int|null $try
     * @return Post[]
     */
    public function loadPageData(int $page, ?int $try = 1): array
    {
        try {
            $data = $this->apiCaller->getData($this->getToken(false), $page);
            return $this->populatePosts($data['data']['posts']);
        } catch (\RuntimeException $e) {
            if ($e->getCode() == self::TOKEN_EXPIRED_CODE) {
                if ($try >= self::MAX_LOAD_TRIES) {
                    throw new \RuntimeException(sprintf('Cannot get token for %s times', $try));
                }
                $this->getToken(true);
                return $this->loadPageData($page, $try + 1);
            }
            throw new \RuntimeException(sprintf('Cannot load page %s', $page));
        }
    }

    /**
     * @param bool $renew
     * @return string
     */
    private function getToken(bool $renew = false): string
    {
        if ($renew || !$this->token) {
            $this->token = $this->apiCaller->getToken();
        }

        return $this->token;
    }

    /**
     * @param array $data
     * @return Post[]
     */
    private function populatePosts(array $data): array
    {
        return array_map(function ($arrayData) {
            return Post::createFromArray($arrayData);
        }, $data);
    }

}