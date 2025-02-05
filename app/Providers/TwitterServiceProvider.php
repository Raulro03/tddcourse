<?php

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Services\NullTwitterClient;
use App\Services\TwitterClient;
use App\Services\TwitterClientInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TwitterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TwitterOAuth::class, function () {
            return new TwitterOAuth(
                (string) config('services.twitter.consumer_key'),
                (string) config('services.twitter.consumer_secret'),
                (string) config('services.twitter.access_token'),
                (string) config('services.twitter.access_token_secret')
            );
        });

        $this->app->bind(TwitterClientInterface::class, function (Application $app) {
            if ($app->environment() === 'production') {
                return app(TwitterClient::class);
            }

            return new NullTwitterClient;
        });
    }

    public function boot(): void {}
}
