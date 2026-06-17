<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── ADMISSION CYCLES ──────────────────────────────────────────────────
        Schema::create('admission_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // e.g. "Fall 2026", "Spring 2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['tenant_id', 'is_active']);
        });

        // ── ADMISSION CAMPAIGNS ──────────────────────────────────────────────
        Schema::create('admission_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('cycle_id')->constrained('admission_cycles')->cascadeOnDelete();
            $table->string('name'); // e.g. "Digital Marketing Campaign", "High School Outreach"
            $table->decimal('budget', 15, 2)->default(0.00);
            $table->integer('target_enrollments')->default(0);
            $table->string('status')->default('PLANNING'); // PLANNING, ACTIVE, COMPLETED, CANCELLED
            $table->timestamps();
            $table->index(['tenant_id', 'cycle_id']);
        });

        // ── ADMISSION OFFERS (Waitlist & Offer letters) ─────────────────────
        Schema::create('admission_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->text('offer_letter_text')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, ACCEPTED, DECLINED, EXPIRED
            $table->timestamps();
            $table->index(['tenant_id', 'admission_id', 'status']);
        });

        // Add cycle_id and campaign_id to admissions table
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->uuid('cycle_id')->nullable()->after('admission_form_id');
                $table->uuid('campaign_id')->nullable()->after('cycle_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->dropColumn(['cycle_id', 'campaign_id']);
            });
        }
        Schema::dropIfExists('admission_offers');
        Schema::dropIfExists('admission_campaigns');
        Schema::dropIfExists('admission_cycles');
    }
};
