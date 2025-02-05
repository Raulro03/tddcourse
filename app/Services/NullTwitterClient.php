<?php

namespace App\Services;

class NullTwitterClient implements TwitterClientInterface
{
    public function tweet(string $status): array
    {
        return [];
    }
}
