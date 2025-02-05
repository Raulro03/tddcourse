<?php

test('calls oauth client for a tweet', function () {
    // Arrange
    $mock = mock(\Abraham\TwitterOAuth\TwitterOAuth::class)
        ->shouldReceive('post')
        ->withArgs(['statuses/update', ['status' => 'My tweet message']])
        ->andReturn(['status' => 'My tweet message'])
        ->getMock();

    // Act
    $twitterClient = (new \App\Services\TwitterClient($mock));

    // Assert
    expect($twitterClient->tweet('My tweet message'))
        ->toEqual(['status' => 'My tweet message']);
});
