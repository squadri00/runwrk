<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->char('endpoint_hash', 64)->unique();
            $table->string('p256dh', 255);
            $table->string('auth', 255);
            $table->string('content_encoding', 20)->default('aes128gcm');
            $table->string('platform', 20)->default('other');
            $table->string('source', 20)->default('hosted');
            $table->string('origin')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->unsignedSmallInteger('fail_count')->default(0);
            $table->timestamps();
            $table->index(['business_id', 'id']);
            $table->index(['business_id', 'created_at']);
        });

        Schema::create('push_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 100);
            $table->string('body', 300);
            $table->string('url', 500)->nullable();
            $table->string('image_path')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->unsignedBigInteger('cursor_id')->default(0);
            $table->unsignedBigInteger('max_id')->default(0);
            $table->unsignedInteger('target_count')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failure_count')->default(0);
            $table->unsignedInteger('expired_count')->default(0);
            $table->unsignedInteger('click_count')->default(0);
            $table->timestamps();
            $table->index(['business_id', 'created_at']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_messages');
        Schema::dropIfExists('push_subscriptions');
    }
};
