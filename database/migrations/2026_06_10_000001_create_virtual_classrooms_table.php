<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_classrooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignUuid('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes');
            $table->string('meeting_id')->unique();
            $table->string('status')->default('UPCOMING'); // UPCOMING, LIVE, COMPLETED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_classrooms');
    }
};
