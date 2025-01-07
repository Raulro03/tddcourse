<?php

use App\Http\Livewire\VideoPlayer;
use App\Models\Course;

use App\Models\Video;
use function Pest\Laravel\get;

it('cannot be accessed by guest', function () {
    // Arrange
    $course = Course::factory()->create();

    // Act & Assert
    get(route('pages.course-videos', $course))
        ->assertRedirect(route('login'));

});

it('includes a video player', function () {
    // Arrange
    $course = Course::factory()
                    ->has(Video::factory())
                    ->create();

    // Act & Assert
    loginAsUser();
    get(route('pages.course-videos', $course))
        ->assertOk()
        ->assertSeeLivewire(VideoPlayer::class);

});

it('shows first course video by default', function () {
    // Arrange
    $course = Course::factory()
        ->has(Video::factory()->state(['title' => 'First Video']))
        ->create();

    // Act & Assert
    loginAsUser();
    get(route('pages.course-videos', $course))
        ->assertOk()
        ->assertSee('First Video');
});

it('shows provided course video', function () {
    // Arrange
    $course = Course::factory()
        ->has(
            Video::factory()
                ->state(new \Illuminate\Database\Eloquent\Factories\Sequence(
                    ['title' => 'First Video'],
                    ['title' => 'Second Video']
                ))
                ->count(2)
        )
        ->create();

    // Act & Assert
    loginAsUser();
    get(route('pages.course-videos', [
        'course' => $course,
        'video' => $course->videos->last()
    ]))
        ->assertOk()
        ->assertSeeText('Second Video');
});
