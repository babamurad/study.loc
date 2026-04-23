# LMS Project

This is a Learning Management System built with Laravel 12 and Livewire 3.

## Setup Instructions

1.  Clone the repository.
2.  Install dependencies: `composer install` and `npm install`.
3.  Create a `.env` file from `.env.example` and configure your database.
4.  Generate an application key: `php artisan key:generate`.
5.  Run database migrations and seed the database with demo content:

    ```bash
    php artisan migrate --seed
    ```

6.  Start the development server: `php artisan serve`.

## Demo Scenario

After running the setup instructions, you can test the "Happy Path" for the course progress functionality:

1.  **Register a new user.** You will be automatically logged in.
2.  **Go to the main page.** You will see the "Laravel 12 + Livewire 3 Masterclass" course.
3.  **Click on the course.** You will see the course page with a list of modules and lessons.
4.  **Verify access.** Only the first lesson, "Installation & Setup", should be accessible. The rest will be locked.
5.  **Complete the first lesson.** Open the first lesson and click the "Завершить урок" (Complete Lesson) button.
6.  **Check for unlock.** You will see a confirmation message. When you return to the course page, the second lesson, "Routing Basics", will be unlocked and available.
7.  **Check the progress bar.** The course progress bar will be updated to reflect your completion.
8.  Continue this process to complete the entire course.
