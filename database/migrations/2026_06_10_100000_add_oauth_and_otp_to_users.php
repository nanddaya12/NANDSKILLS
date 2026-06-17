<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add OAuth and phone OTP columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('social_provider')->nullable()->after('phone');     // google | github | facebook
            $table->string('social_provider_id')->nullable()->after('social_provider');
            $table->string('avatar_url')->nullable()->after('social_provider_id');
            $table->string('phone_verified_at')->nullable()->after('avatar_url');
        });

        // OTP store for phone-based sign in
        Schema::create('phone_otps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 20);
            $table->string('otp', 10);
            $table->string('purpose')->default('LOGIN');   // LOGIN | REGISTER
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();
            $table->index(['phone', 'otp']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['social_provider', 'social_provider_id', 'avatar_url', 'phone_verified_at']);
        });
        Schema::dropIfExists('phone_otps');
    }
};
