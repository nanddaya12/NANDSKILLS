<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->decimal('price', 10, 2);
            $table->string('billing_interval');
            $table->integer('max_users');
            $table->integer('max_courses');
            $table->bigInteger('max_storage_bytes');
            $table->jsonb('features');
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->unique()->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, SUSPENDED, PENDING
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('primary_color')->default('#2563EB');
            $table->string('secondary_color')->default('#0F172A');
            $table->string('typography')->default('Inter');
            $table->string('dashboard_theme')->default('light');
            $table->text('email_template')->nullable();
            $table->string('website_theme')->default('default');
            $table->foreignUuid('plan_id')->constrained('tenant_plans');
            $table->timestamps();
        });

        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('plan_id')->constrained('tenant_plans');
            $table->string('status')->default('TRIAL'); // TRIAL, ACTIVE, PAST_DUE, CANCELED, UNPAID
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('email');
            $table->string('password_hash');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->boolean('mfa_enabled')->default(false);
            $table->string('mfa_secret')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, SUSPENDED, INVITED
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['tenant_id', 'email']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->unique(['tenant_id', 'email']);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('tenant_subscriptions');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('tenant_plans');
    }
};
