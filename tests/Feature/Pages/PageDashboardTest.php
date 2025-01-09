<?php

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;

it('cannot be accessed by guest', function () {
    // Act & Assert
    get(route('pages.dashboard'))
        ->assertRedirect(route('login'));
});

it('lists purchased courses', function () {
    // Arrange
    $user = User::factory()
                ->has(Course::factory()->count(2)->state(
                    new Sequence(
                        ['title' => 'Course A'],
                        ['title' => 'Course B'],
                    )), 'purchasedCourses')
                ->create();

    // Act & Assert
    loginAsUser($user);
    get(route('pages.dashboard'))
        ->assertOk()
        ->assertSeeText([
            'Course A',
            'Course B',
        ]);
});

it('does not list other courses', function () {
    // Arrange
    $course = Course::factory()->create();

    // Act & Assert
    loginAsUser();
    get(route('pages.dashboard'))
        ->assertOk()
        ->assertDontSeeText($course->title);

});

it('shows latest purchased course first', function () {
    // Arrange
    $user = loginAsUser();
    $firstPurchasedCourse = Course::factory()->create();
    $secondPurchasedCourse = Course::factory()->create();

    $user->purchasedCourses()->attach($firstPurchasedCourse, ['created_at' => Carbon::yesterday()]);
    $user->purchasedCourses()->attach($secondPurchasedCourse, ['created_at' => Carbon::now()]);

    // Act & Assert
    get(route('pages.dashboard'))
        ->assertOk()
        ->assertSeeTextInOrder([
            $secondPurchasedCourse->title,
            $firstPurchasedCourse->title,
        ]);
});

it('includes link to product videos', function () {
    // Arrange
    $user = User::factory()
        ->has(Course::factory(), 'purchasedCourses')
        ->create();

    // Act & Assert
    loginAsUser($user);
    get(route('pages.dashboard'))
        ->assertOk()
        ->assertSeeText('Watch videos')
        ->assertSee(route('pages.course-videos', Course::first()));
});

it('includes logout', function () {
    // Act & Assert
    loginAsUser();
    get(route('pages.dashboard'))
        ->assertOk()
        ->assertSeeText('Log Out')
        ->assertSee(route('logout'));
});
