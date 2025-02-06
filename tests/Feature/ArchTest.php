<?php


it('finds missing debug statements', function () {
    // Act & Assert
    expect(['dd', 'dump', 'ray'])
        ->not->toBeUsed();
});

it('does not use validator facade', function () {
    // Act & Assert
    expect(\Illuminate\Support\Facades\Validator::class)
        ->not->toBeUsed()
        ->ignoring('App\Actions\Fortify');
});
