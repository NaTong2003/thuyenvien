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
        Schema::table('thuyen_viens', function (Blueprint $table) {
            $table->foreignId('ship_type_id')->nullable()->after('position_id')
                  ->constrained('ship_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thuyen_viens', function (Blueprint $table) {
            $table->dropForeign(['ship_type_id']);
            $table->dropColumn('ship_type_id');
        });
    }
};
