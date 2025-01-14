<?php


use App\Models\Course;
use App\Models\User;
use App\Models\Video;

it('adds given courses', function () {
    // Assert
    $this->assertDatabaseCount(Course::class, 0);

    // Act
    $this->artisan('db:seed');

    // Assert
    $this->assertDatabaseCount(Course::class, 3);
    $this->assertDatabaseHas(Course::class, ['title' => 'Laravel For Beginners']);
    $this->assertDatabaseHas(Course::class, ['title' => 'Advanced Laravel']);
    $this->assertDatabaseHas(Course::class, ['title' => 'TDD The Laravel Way']);
});

it('adds given courses only once', function () {
    // Act
    $this->artisan('db:seed');
    $this->artisan('db:seed');

    // Assert
    $this->assertDatabaseCount(Course::class, 3);
});

it('adds given videos', function () {
    // Assert
    $this->assertDatabaseCount(Video::class, 0);

    // Act
    $this->artisan('db:seed');
    $laravelForBeginners = Course::where('title', 'Laravel For Beginners')->firstOrFail();
    $advancedLaravel = Course::where('title', 'Advanced Laravel')->firstOrFail();
    $tddTheLaravelWay = Course::where('title', 'TDD The Laravel Way')->firstOrFail();

    // Assert
    $this->assertDatabaseCount(Video::class, 8);
    expect($laravelForBeginners)
        ->videos
        ->toHaveCount(3);

    expect($advancedLaravel)
        ->videos
        ->toHaveCount(3);

    expect($tddTheLaravelWay)
        ->videos
        ->toHaveCount(2);
});

it('adds given videos only once', function () {
    // Assert
    $this->assertDatabaseCount(Video::class, 0);

    // Act
    $this->artisan('db:seed');
    $this->artisan('db:seed');

    // Assert
    $this->assertDatabaseCount(Video::class, 8);
});

it('adds local test user', function () {
    // Arrange
    App::partialMock()->shouldReceive('environment')->andReturn('local');

    // Assert
    $this->assertDatabaseCount(User::class, 0);

    // Act
    $this->artisan('db:seed');

    // Assert
    $this->assertDatabaseCount(User::class, 1);
});

it('does not test user for production', function () {
    // Arrange
    App::partialMock()->shouldReceive('environment')->andReturn('production');

    // Assert
    $this->assertDatabaseCount(User::class, 0);

    // Act
    $this->artisan('db:seed');

    // Assert
    $this->assertDatabaseCount(User::class, 0);
});
