<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->jsonb('content'); // Page builder layout configuration
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->boolean('is_system')->default(false);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->text('summary')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, ARCHIVED
            $table->timestamp('published_at')->nullable();
            $table->foreignUuid('author_id')->constrained('users');
            $table->integer('view_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('blog_post_categories', function (Blueprint $table) {
            $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignUuid('blog_category_id')->constrained('blog_categories')->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_category_id']);
        });

        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignUuid('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });

        Schema::create('media_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('storage_key');
            $table->string('file_url');
            $table->string('file_type');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->string('folder_path')->default('/');
            $table->foreignUuid('uploaded_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('form_builders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->jsonb('fields'); // Schema definition for input fields
            $table->jsonb('settings')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('form_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('form_builders')->cascadeOnDelete();
            $table->jsonb('data');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('kb_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->uuid('parent_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('kb_articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->foreignUuid('category_id')->references('id')->on('kb_categories')->cascadeOnDelete();
            $table->integer('view_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // LANDING, POPUP, BANNER, NEWSLETTER
            $table->jsonb('content');
            $table->jsonb('settings')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('kb_articles');
        Schema::dropIfExists('kb_categories');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_builders');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('blog_post_tags');
        Schema::dropIfExists('blog_post_categories');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('website_pages');
    }
};
