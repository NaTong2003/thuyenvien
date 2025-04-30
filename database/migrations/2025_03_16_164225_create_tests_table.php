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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ship_type_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('duration')->default(60); // thời gian làm bài (phút)
            $table->boolean('is_random')->default(false); // chọn ngẫu nhiên câu hỏi hay không
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['certification', 'assessment', 'placement', 'practice'])->default('practice');
            $table->enum('difficulty', ['Dễ', 'Trung bình', 'Khó'])->default('Trung bình');
            $table->integer('passing_score')->nullable(); // điểm đạt (%)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('random_questions_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
