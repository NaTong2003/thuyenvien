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
        Schema::table('test_questions', function (Blueprint $table) {
            $table->boolean('is_temporary')->default(false)->after('order');
            $table->foreignId('test_attempt_id')->nullable()->after('is_temporary')
                  ->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropForeign(['test_attempt_id']);
            $table->dropColumn(['is_temporary', 'test_attempt_id']);
        });
    }
};
