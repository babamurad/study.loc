<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_practice_id')->constrained()->onDelete('cascade');
            $table->text('html_code')->nullable();
            $table->text('css_code')->nullable();
            $table->text('js_code')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'timeout'])->default('pending');
            $table->decimal('score', 3, 1)->nullable();
            $table->boolean('passed')->default(false);
            $table->unsignedInteger('attempt_no')->default(1);
            $table->string('runner_job_id')->nullable();
            $table->string('runner_version')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('raw_result')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'lesson_practice_id']);
            $table->index(['lesson_practice_id', 'status']);
            $table->index('runner_job_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_submissions');
    }
};