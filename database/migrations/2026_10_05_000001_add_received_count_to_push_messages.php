<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('push_messages', function (Blueprint $table) {
            $table->unsignedInteger('received_count')->default(0)->after('expired_count');
        });
    }

    public function down(): void
    {
        Schema::table('push_messages', fn (Blueprint $table) => $table->dropColumn('received_count'));
    }
};
