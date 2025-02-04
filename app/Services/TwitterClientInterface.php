<?php

namespace App\Services;

interface TwitterClientInterface
{
    public function tweet(string $status): array;
}
