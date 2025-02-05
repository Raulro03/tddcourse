<?php


it('returns null twitter client for testing env', function () {
    // Act & Assert
    expect(app(\App\Services\TwitterClientInterface::class))
        ->toBeInstanceOf(\App\Services\NullTwitterClient::class);
});
