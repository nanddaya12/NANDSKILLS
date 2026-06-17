<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_violations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->string('violation_type'); // TAB_SWITCH, FULLSCREEN_EXIT, IP_CHANGE
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_violations');
    }
};
