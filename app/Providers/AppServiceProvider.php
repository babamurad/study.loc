<?php

namespace App\Providers;

use App\Contracts\PracticeEvaluatorInterface;
use App\Services\Evaluators\GeminiEvaluator;
use App\Services\Evaluators\LocalPhpEvaluator;
use App\Services\Evaluators\NodeRunnerEvaluator;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        $this->registerPracticeEvaluator();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): Password => Password::min(8));
    }

    /**
     * Register the practice evaluator based on the configured driver.
     */
    protected function registerPracticeEvaluator(): void
    {
        $this->app->bind(PracticeEvaluatorInterface::class, function () {
            $driver = config('services.practice_evaluator.driver', 'local');

            return match ($driver) {
                'gemini' => new GeminiEvaluator(),
                'node'   => new NodeRunnerEvaluator(),
                default => new LocalPhpEvaluator(),
            };
        });
    }
}
