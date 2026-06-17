<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_email');
            $table->timestamps();
        });

        Schema::create('placement_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('employer_profile_id')->constrained('employer_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('FULL_TIME'); // FULL_TIME, PART_TIME, INTERNSHIP
            $table->string('salary_range')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, CLOSED
            $table->timestamps();
        });

        Schema::create('placement_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('placement_job_id')->constrained('placement_jobs')->cascadeOnDelete();
            $table->foreignUuid('student_profile_id')->references('id')->on('student_profiles')->cascadeOnDelete();
            $table->text('resume_text')->nullable(); // Simple resume compiler details
            $table->string('status')->default('PENDING'); // PENDING, SHORTLISTED, REJECTED, ACCEPTED
            $table->dateTime('interview_scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_applications');
        Schema::dropIfExists('placement_jobs');
        Schema::dropIfExists('employer_profiles');
    }
};
