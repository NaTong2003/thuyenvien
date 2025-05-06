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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('test_attempt_id')->nullable()->constrained()->onDelete('set null');
            $table->string('certificate_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('active'); // active, revoked, expired
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('revocation_reason')->nullable();
            $table->string('certificate_file')->nullable(); // đường dẫn đến file PDF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
