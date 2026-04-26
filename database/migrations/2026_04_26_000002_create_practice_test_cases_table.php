<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_practice_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['dom', 'css', 'behavior', 'console_errors', 'snapshot'])->default('dom');
            $table->decimal('weight', 3, 1)->default(2.0);
            $table->json('script');
            $table->unsignedInteger('timeout_ms')->default(1000);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->string('version')->default('1.0');
            $table->timestamps();

            $table->index('lesson_practice_id');
            $table->index(['lesson_practice_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_test_cases');
    }
};