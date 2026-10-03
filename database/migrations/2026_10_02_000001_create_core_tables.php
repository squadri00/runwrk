<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 20)->default('trial')->index();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_key', 64)->unique();
            $table->string('short_name', 30)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('icon_path')->nullable();
            $table->string('theme_color', 9)->default('#111827');
            $table->string('background_color', 9)->default('#ffffff');
            $table->string('phone', 40)->nullable();
            $table->string('address')->nullable();
            $table->string('website_url')->nullable();
            $table->json('hours')->nullable();
            $table->string('timezone', 64)->default('America/Toronto');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('business_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('domain');
            $table->timestamps();
            $table->unique(['business_id', 'domain']);
            $table->index('domain');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role', 20)->default('owner');
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('superadmins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type', 10)->default('string');
            $table->timestamps();
            $table->unique(['business_id', 'key']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('actor_type')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_label')->nullable();
            $table->unsignedBigInteger('impersonated_by')->nullable();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['business_id', 'created_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        foreach (['sessions', 'password_reset_tokens', 'audit_logs', 'settings', 'superadmins', 'users', 'business_domains', 'businesses', 'plans'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
