<?php

test('returns empty array for a tweet call', function () {
    expect(new \App\Services\NullTwitterClient())
        ->tweet('Our tweet')
        ->toBeArray()
        ->toBeEmpty();
});
