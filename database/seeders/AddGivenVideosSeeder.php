<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Video;
use Illuminate\Database\Seeder;

class AddGivenVideosSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->isDataAlreadyGiven()) {
            return;
        }

        $laravelForBeginnersCourse = Course::where('title', 'Laravel For Beginners')->first();
        $advancedLaravelCourse = Course::where('title', 'Advanced Laravel')->first();
        $tddTheLaravelWayCourse = Course::where('title', 'TDD The Laravel Way')->first();

        Video::insert([
            [
                'course_id' => $laravelForBeginnersCourse->id,
                'slug' => 'intro',
                'vimeo_id' => '330287829',
                'title' => 'Intro',
                'description' => 'Welcome to this course. Let me tell you that you are going to learn in the next videos.',
                'duration_in_min' => 1,
            ],
            [
                'course_id' => $laravelForBeginnersCourse->id,
                'slug' => 'routes',
                'vimeo_id' => '329875646',
                'title' => 'Routes',
                'description' => 'Routes are fundamental to every web application. Laravel provides a simple syntax to define them.',
                'duration_in_min' => 4,
            ],
            [
                'course_id' => $laravelForBeginnersCourse->id,
                'slug' => 'views',
                'vimeo_id' => '331956991',
                'title' => 'Views',
                'description' => 'We cannot talk about routes without talking about views. Together they will make sure we can show our users a beautiful response.',
                'duration_in_min' => 7,
            ],
            [
                'course_id' => $advancedLaravelCourse->id,
                'slug' => 'intro',
                'vimeo_id' => '333506858',
                'title' => 'Intro',
                'description' => 'Welcome to Advanced Laravel. Are you ready to level up your Laravel skills?',
                'duration_in_min' => 2,
            ],
            [
                'course_id' => $advancedLaravelCourse->id,
                'slug' => 'the-service-container',
                'vimeo_id' => '333173142',
                'title' => 'The Service Container',
                'description' => 'The service container is one of the most powerful concepts of Laravel, but at the same time, one of the most difficult concepts to grasp. Let me help you.',
                'duration_in_min' => 12,
            ],
            [
                'course_id' => $advancedLaravelCourse->id,
                'slug' => 'the-pipeline-pattern',
                'vimeo_id' => '737854276',
                'title' => 'The Pipeline Pattern',
                'description' => 'Have you ever heard of the Pipeline Pattern? No? Let us fix this today.',
                'duration_in_min' => 8,
            ],
            [
                'course_id' => $tddTheLaravelWayCourse->id,
                'slug' => 'phpunit-framework',
                'vimeo_id' => '737854155',
                'title' => 'PHPUnit Framework',
                'description' => 'You will learn about phpunit.',
                'duration_in_min' => 10,
            ],
            [
                'course_id' => $tddTheLaravelWayCourse->id,
                'slug' => 'pest-framework',
                'vimeo_id' => '444173142',
                'title' => 'PEST Framework',
                'description' => 'You will learn about PEST framework, a modern tool for testing.',
                'duration_in_min' => 9,
            ],
        ]);
    }

    private function isDataAlreadyGiven(): bool
    {
        $laravelForBeginnersCourse = Course::where('title', 'Laravel For Beginners')->first();
        $advancedLaravelCourse = Course::where('title', 'Advanced Laravel')->first();
        $tddTheLaravelWayCourse = Course::where('title', 'TDD The Laravel Way')->first();

        return $laravelForBeginnersCourse->videos()->count() === 3
            && $advancedLaravelCourse->videos()->count() === 3
            && $tddTheLaravelWayCourse->videos()->count() === 2;
    }
}
