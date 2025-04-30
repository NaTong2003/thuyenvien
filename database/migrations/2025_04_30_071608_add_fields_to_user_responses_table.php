<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_responses', function (Blueprint $table) {
            $table->string('response_type')->nullable()->after('text_response');
            $table->text('admin_comment')->nullable()->after('score');
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->boolean('needs_marking')->default(false)->after('is_completed');
            $table->boolean('is_marked')->default(false)->after('needs_marking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_responses', function (Blueprint $table) {
            $table->dropColumn(['response_type', 'admin_comment']);
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn(['needs_marking', 'is_marked']);
        });
    }
};
