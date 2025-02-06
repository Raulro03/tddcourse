<?php

use App\Models\Course;
use App\Models\Video;

use function Pest\Laravel\get;

it('gives back successful response for home page', function () {
    get(route('pages.home'))
        ->assertOk();
});

it('gives back successful response for course details page', function () {
    // Arrange
    $course = Course::factory()->released()->create();

    // Act
    get(route('pages.course-details', $course))
        ->assertOk();
});

it('gives back successful for dashboard page', function () {
    // Act & Assert
    loginAsUser();

    get(route('pages.dashboard'))
        ->assertOk();
});

it('does not find Jetstream registration page', function () {
    // Act & assert
    get('register')
        ->assertNotFound();
});

it('gives successful response for videos page', function () {
    // Arrange
    $course = Course::factory()
        ->has(Video::factory())
        ->create();

    // Act & Assert
    loginAsUser();
    get(route('pages.course-videos', $course))
        ->assertOk();
});

it('is a todo', function () {
    // Arrange

    // Act & Assert
})->todo();
