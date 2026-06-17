<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE
            $table->timestamps();
        });

        Schema::create('branch_admins', function (Blueprint $table) {
            $table->foreignUuid('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['branch_id', 'user_id']);
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('roll_number');
            $table->date('admission_date');
            $table->jsonb('academic_record')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, SUSPENDED, GRADUATED
            $table->timestamps();
        });

        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('relationship'); // Father, Mother, Guardian
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('student_parents', function (Blueprint $table) {
            $table->foreignUuid('student_id')->references('id')->on('student_profiles')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->references('id')->on('parent_profiles')->cascadeOnDelete();
            $table->primary(['student_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
        Schema::dropIfExists('parent_profiles');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('branch_admins');
        Schema::dropIfExists('branches');
    }
};
