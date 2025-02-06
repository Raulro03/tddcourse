<?php

use App\Models\Course;
use Illuminate\Support\Carbon;

use Juampi92\TestSEO\TestSEO;
use function Pest\Laravel\get;

it('shows courses overview', function () {
    // Arrange
    $firstCourse = Course::factory()->released()->create();
    $secondCourse = Course::factory()->released()->create();
    $thirdCourse = Course::factory()->released()->create();

    // Act
    get(route('pages.home'))
        ->assertSeeText([
            $firstCourse->title,
            $firstCourse->description,
            $secondCourse->title,
            $secondCourse->description,
            $thirdCourse->title,
            $thirdCourse->description,
        ]);
});

it('shows only released courses', function () {
    // Arrange
    $releasedCourse = Course::factory()->released()->create();
    $notReleasedCourse = Course::factory()->create();

    // Act
    get(route('pages.home'))
        ->assertSeeText($releasedCourse->title)
        ->assertDontSeeText($notReleasedCourse->title);
});

it('shows courses by release date', function () {
    // Arrange
    $releasedCourse = Course::factory()->released(Carbon::yesterday())->create();
    $newestReleasedCourse = Course::factory()->released()->create();

    // Act
    get(route('pages.home'))
        ->assertSeeTextInOrder([
            $newestReleasedCourse->title,
            $releasedCourse->title,
        ]);
});

it('includes login if not logged in', function () {
    // Act & Assert
    get(route('pages.home'))
        ->assertOk()
        ->assertSeeText('Login')
        ->assertSee(route('login'));

});

it('includes logout if logged in', function () {
    // Act & Assert
    loginAsUser();
    get(route('pages.home'))
        ->assertOk()
        ->assertSeeText('Dashboard')
        ->assertSee(route('pages.dashboard'));

});

it('includes courses links', function () {
    // Arrange
    $firstCourse = Course::factory()->released()->create();
    $secondCourse = Course::factory()->released()->create();
    $thirdCourse = Course::factory()->released()->create();

    // Act & Assert
    get(route('pages.home'))
        ->assertOk()
        ->assertSee([
            route('pages.course-details', $firstCourse),
            route('pages.course-details', $secondCourse),
            route('pages.course-details', $thirdCourse),
        ]);
});

it('includes title', function () {
    // Arrange
    $expectedTitle = config('app.name') . ' - Home';

    // Act
    $response = get(route('pages.home'))
        ->assertOk();

    // Assert
    $seo = new TestSEO($response->getContent());
    expect($seo->data)
        ->title()->toBe($expectedTitle);
});

it('includes social tags', function () {
    // Act
    $response = get(route('pages.home'))
        ->assertOk();

    // Assert
    $seo = new TestSEO($response->getContent());
    expect($seo->data)
        ->description()->toBe('TDDCourseIES is the leading learning platform for Laravel developers')
        ->openGraph()->type()->toBe('website')
        ->openGraph()->url()->toBe(route('pages.home'))
        ->openGraph()->title()->toBe('TDDCourseIES')
        ->openGraph()->description()->toBe('TDDCourseIES is the leading learning platform for Laravel developers')
        ->openGraph()->image()->toBe(asset('images/social.png'))
        ->twitter()->card->toBe('summary_large_image');
});
