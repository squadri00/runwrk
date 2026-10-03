<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->text('description')->nullable()->after('code');
            $table->unsignedInteger('price_cents')->default(0)->after('description');
            $table->string('currency', 3)->default('usd')->after('price_cents');
            $table->string('interval', 10)->default('month')->after('currency');
            $table->string('stripe_price_id')->nullable()->after('interval');
            $table->boolean('is_public')->default(true)->after('features');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_public');
            $table->timestamp('archived_at')->nullable()->after('sort_order');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('hours');
        });

        Schema::table('superadmins', function (Blueprint $table) {
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_confirmed_at');
        });

        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('name');
            $table->string('business_name');
            $table->string('email')->index();
            $table->string('password');
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('otp_code', 255)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->unsignedTinyInteger('otp_attempts')->default(0);
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
        Schema::table('superadmins', fn (Blueprint $t) => $t->dropColumn('two_factor_recovery_codes'));
        Schema::table('businesses', fn (Blueprint $t) => $t->json('hours')->nullable());
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['description', 'price_cents', 'currency', 'interval', 'stripe_price_id', 'is_public', 'sort_order', 'archived_at']);
            $table->boolean('active')->default(true);
        });
    }
};
