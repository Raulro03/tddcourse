<?php


use App\Http\Livewire\VideoPlayer;
use App\Models\Course;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;

function createCourseAndVideos(int $videosCount = 1): Course
{
    return Course::factory()
                 ->has(Video::factory()->count($videosCount))
                 ->create();
}

beforeEach(function () {
    $this->logguedInUser = loginAsUser();
});

it('shows details for given video', function () {
    // Arrange
    $course = createCourseAndVideos();

    // Act & Assert
    $video = $course->videos->first();
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertSeeText([
            $video->title,
            $video->description,
            "({$video->duration_in_min}min)"
        ]);

});

it('shows given video', function () {
    // Arrange
    $course = createCourseAndVideos();

    // Act & Assert
    $video = $course->videos->first();
    Livewire::test(VideoPlayer::class, ['video' => $video])
        ->assertSeeHtml('<iframe src="https://player.vimeo.com/video/' . $video->vimeo_id . '"');

});

it('shows list of all course videos', function () {
    // Arrange
    $course = createCourseAndVideos(3);

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos()->first()])
        ->assertSee($course->videos->pluck('title')->toArray())
        ->assertSeeHtml([
            route('pages.course-videos', $course->videos[1]),
            route('pages.course-videos', $course->videos[2]),
        ]);
});

it('marks video as completed', function () {
    // Arrange
    $course = createCourseAndVideos();

    $this->logguedInUser->purchasedCourses()->attach($course);

    // Assert
    expect($this->logguedInUser->watchedVideos)->toHaveCount(0);

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertMethodWired('markVideoAsCompleted')
        ->call('markVideoAsCompleted')
        ->assertMethodWired('markVideoAsNotCompleted')
        ->assertMethodNotWired('markVideoAsCompleted');

    // Assert
    $this->logguedInUser->refresh();
    expect($this->logguedInUser->watchedVideos)
        ->toHaveCount(1)
        ->first()->title->toEqual($course->videos->first()->title);

});

it('marks video as not completed', function () {
    // Arrange
    $course = createCourseAndVideos();

    $this->logguedInUser->purchasedCourses()->attach($course);
    $this->logguedInUser->watchedVideos()->attach($course->videos->first());

    // Assert
    expect($this->logguedInUser->watchedVideos)->toHaveCount(1);

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertMethodWired('markVideoAsNotCompleted')
        ->call('markVideoAsNotCompleted')
        ->assertMethodWired('markVideoAsCompleted')
        ->assertMethodNotWired('markVideoAsNotCompleted');

    // Assert
    $this->logguedInUser->refresh();
    expect($this->logguedInUser->watchedVideos)->toHaveCount(0);
});

it('does not include route for current video', function () {
    // Arrange
    $course = createCourseAndVideos();

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertDontSeeHtml(route('pages.course-videos', $course->videos->first()));
});
