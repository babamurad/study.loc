<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_practices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('runner_profile')->default('frontend_html_css_js_v1');
            $table->decimal('max_score', 3, 1)->default(10.0);
            $table->decimal('pass_score', 3, 1)->default(7.0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('lesson_id');
            $table->index(['lesson_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_practices');
    }
};