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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->enum('type', ['Trắc nghiệm', 'Tự luận', 'Tình huống', 'Mô phỏng', 'Thực hành']);
            $table->enum('difficulty', ['Dễ', 'Trung bình', 'Khó']);
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ship_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
