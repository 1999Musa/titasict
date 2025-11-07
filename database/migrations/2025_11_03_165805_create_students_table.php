<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_time_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('mobile_number')->unique();
            $table->string('guardian_mobile_number')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->string('exam_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
