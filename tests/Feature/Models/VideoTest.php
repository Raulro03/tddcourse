<?php


use App\Models\{Video, Course};

it('has course', function () {
    // Arrange
    $video = Video::factory()
                  ->has(Course::factory())
                  ->create();

    // Act & Assert
    expect($video->course)
        ->toBeInstanceOf(Course::class);
});

it('gives back readable video duration', function () {
    // Arrange
    $video = Video::factory()->create(['duration_in_min' => 10]);

    // Act & Assert
    expect($video->getReadableDuration())->toEqual('10min');

});
