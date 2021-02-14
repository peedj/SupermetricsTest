<?php

namespace ApiConnect\connectors\interfaces;

interface ApiCallInterface
{
    public function getToken(): string;

    public function getData(string $token, int $page = 1): array;
}