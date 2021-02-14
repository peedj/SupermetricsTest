<?php

namespace ApiConnect\helpers;

/**
 * Class UrlCall
 * @package ApiConnect\helpers
 */
class UrlCaller
{
    public ?string $error = null;
    public ?int $errno = null;

    /**
     * @param string $url
     * @param array|null $additional_headers
     * @return string|null
     */
    public function call(string $url, ?array $additional_headers = []): ?string {
        $this->error = null;
        $this->errno = null;

        $curl = curl_init();

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
        ] + $additional_headers;

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $this->error = curl_error($curl);

        if ($this->errno = curl_errno($curl)) {
            $this->error = curl_error($curl);
            $response = '';
        }

        curl_close($curl);
        return $response;
    }
}