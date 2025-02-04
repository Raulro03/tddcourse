<?php

namespace App\Services;

class NullTwitter implements TwitterClientInterface
{
    public function tweet(string $status): array
    {
        return [];
    }
}
