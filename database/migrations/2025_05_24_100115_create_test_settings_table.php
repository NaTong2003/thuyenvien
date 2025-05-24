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
        Schema::create('test_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->boolean('shuffle_questions')->default(false); // Xáo trộn câu hỏi
            $table->boolean('shuffle_answers')->default(false); // Xáo trộn đáp án
            $table->boolean('allow_back')->default(true); // Cho phép quay lại câu trước
            $table->boolean('show_result_immediately')->default(false); // Hiển thị kết quả ngay sau khi làm
            $table->integer('max_attempts')->nullable(); // Tối đa số lần làm, null = không giới hạn
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_settings');
    }
};
