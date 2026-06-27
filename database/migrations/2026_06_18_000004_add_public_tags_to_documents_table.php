<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('share_token')->nullable()->unique()->after('slug');
            $table->boolean('is_public')->default(false)->after('share_token');
            $table->json('tags')->nullable()->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'is_public', 'tags']);
        });
    }
};
