<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;
use Twitter;

class TwitterAboutCourseReleaseCommand extends Command
{
    protected $signature = 'twitter:about-course-release {courseId}';

    protected $description = 'Command description';

    public function handle(): void
    {
        $course = Course::findOrFail($this->argument('courseId'));

        Twitter::tweet("I just released {$course->title}! Check it out on: " . route('pages.course-details', $course));
    }
}
