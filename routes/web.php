<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Home;

Route::get('/', Home::class)->name('home');
Route::get('/debug-session', function () {
    return [
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'cookie_domain' => config('session.domain'),
        'cookie_path' => config('session.path'),
        'cookie_secure' => config('session.secure'),
    ];
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Student Routes
    Route::get('/courses', \App\Livewire\CourseIndex::class)->name('courses.index');

    // Teacher Routes
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Teacher\Dashboard::class)->name('dashboard');
        
        // Courses
        Route::get('/courses', \App\Livewire\Teacher\Courses\Index::class)->name('courses.index');
        Route::get('/courses/create', \App\Livewire\Teacher\Courses\Create::class)->name('courses.create');
        Route::get('/courses/{course}/edit', \App\Livewire\Teacher\Courses\Edit::class)->name('courses.edit');

        // Modules
        Route::get('/modules', \App\Livewire\Teacher\Modules\Index::class)->name('modules.index');
        Route::get('/modules/create', \App\Livewire\Teacher\Modules\Create::class)->name('modules.create');
        Route::get('/modules/{module}/edit', \App\Livewire\Teacher\Modules\Edit::class)->name('modules.edit');

        // Lessons
        Route::get('/lessons', \App\Livewire\Teacher\Lessons\Index::class)->name('lessons.index');
        Route::get('/lessons/create', \App\Livewire\Teacher\Lessons\Create::class)->name('lessons.create');
        Route::get('/lessons/{lesson}/edit', \App\Livewire\Teacher\Lessons\Edit::class)->name('lessons.edit');
    });
});

require __DIR__.'/courses.php';
require __DIR__.'/settings.php';