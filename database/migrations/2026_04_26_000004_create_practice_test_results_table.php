<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('practice_test_case_id')->constrained()->onDelete('cascade');
            $table->boolean('passed')->default(false);
            $table->decimal('earned_weight', 3, 1)->default(0.0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->string('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('practice_submission_id');
            $table->index('practice_test_case_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_test_results');
    }
};