<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = Course::create([
            'title' => 'Laravel 12 + Livewire 3 Masterclass',
            'slug' => 'laravel-12-livewire-3-masterclass',
            'description' => 'A comprehensive course to master Laravel 12 and Livewire 3 from scratch.',
            'is_published' => true,
        ]);

        $module1 = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1: Laravel Fundamentals',
            'position' => 1,
        ]);

        $module2 = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 2: Advanced Livewire',
            'position' => 2,
        ]);

        $lessons = [
            // Module 1
            1 => [
                'module_id' => $module1->id,
                'title' => 'Installation & Setup',
                'content' => '<h1>Welcome to the course!</h1><p>Let\'s start by setting up our Laravel project.</p>',
            ],
            [
                'module_id' => $module1->id,
                'title' => 'Routing Basics',
                'content' => '<h2>Understanding Routes</h2><p>Learn how to define and handle routes in Laravel.</p>',
            ],
            [
                'module_id' => $module1->id,
                'title' => 'Eloquent ORM Introduction',
                'content' => '<h2>Working with Databases</h2><p>An introduction to Laravel\'s powerful ORM, Eloquent.</p>',
            ],
            [
                'module_id' => $module1->id,
                'title' => 'Blade Templating',
                'content' => '<h2>Frontend with Blade</h2><p>Master the Blade templating engine for beautiful views.</p>',
            ],
            [
                'module_id' => $module1->id,
                'title' => 'Your First Livewire Component',
                'content' => '<h2>Intro to Livewire</h2><p>Let\'s build our first reactive component with Livewire.</p>',
            ],
            // Module 2
            [
                'module_id' => $module2->id,
                'title' => 'Reactivity & Data Binding',
                'content' => '<h2>Deep Dive into Reactivity</h2><p>Understand how `wire:model` works under the hood.</p>',
            ],
            [
                'module_id' => $module2->id,
                'title' => 'Events & Listeners',
                'content' => '<h2>Component Communication</h2><p>Learn how to communicate between Livewire components using events.</p>',
            ],
            [
                'module_id' => $module2->id,
                'title' => 'Forms & Validation',
                'content' => '<h2>Real-time Validation</h2><p>Create beautiful and reactive forms with real-time validation.</p>',
            ],
            [
                'module_id' => $module2->id,
                'title' => 'File Uploads',
                'content' => '<h2>Handling File Uploads</h2><p>Learn how to handle file uploads with Livewire seamlessly.</p>',
            ],
            [
                'module_id' => $module2->id,
                'title' => 'Deployment',
                'content' => '<h2>Go Live!</h2><p>Finally, let\'s deploy our application to production.</p>',
            ],
        ];

        foreach ($lessons as $position => $lessonData) {
            Lesson::create([
                'course_id' => $course->id,
                'module_id' => $lessonData['module_id'],
                'title' => $lessonData['title'],
                'slug' => Str::slug($lessonData['title']),
                'content' => $lessonData['content'],
                'position' => $position,
                'is_published' => true,
            ]);
        }
    }
}
