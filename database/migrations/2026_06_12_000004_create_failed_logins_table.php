<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('email');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('portal'); // e.g. STUDENT, STAFF, SUPERADMIN
            $table->string('reason')->nullable(); // INVALID_CREDENTIALS, INACTIVE, INSUFFICIENT_ROLE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_logins');
    }
};
