<?php


use App\Http\Livewire\VideoPlayer;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use function Pest\Laravel\get;

it('cannot be accesses by guest', function () {
    //Arrange

    $course = Course::factory()->create();

    //Act && Assert
    get(route('pages.course-videos', $course))
    ->assertRedirect(route('login'));

});
it('includes a video player', function () {
    //Arrange
    $course = Course::factory()->create();

    //Act && Assert
    loginAsUser();
    get(route('pages.course-videos', $course))
        ->assertOk()
        ->assertSeeLivewire(VideoPlayer::class);
});
it('shows first course video by default', function () {
    //Arrange
    $course = Course::factory()
        ->has(Video::factory()->state(['title' => 'First course video']))
        ->create();

    /*dd($course->videos->first()->title);
    Importante para saber que hay en cada momento porque a veces la prueba puede pasar
    y en ver no deberia de pasar solo que el error no es suficiente */

    //Act && Assert
    loginAsUser();
    get(route('pages.course-videos', $course))
        ->assertOk()
        ->assertSee($course->videos->first()->title);
});
it('shows provided course video', function () {
    //Arrange
    $course = Course::factory()
        ->has(Video::factory()->state(new Sequence([
            'title' => 'First course video',
            ],['title' => 'Second course video'])
        )->count(2)
        )->create();

    //Act && Assert
    loginAsUser();
    get(route('pages.course-videos', [
        'course' => $course,
        'video' => $course->videos->last()
    ]))->assertOk()
        ->assertSee('Second course video');
});
