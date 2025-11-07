<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_day_id')->constrained()->cascadeOnDelete();
            $table->string('time'); // e.g., "9am-10am"
            $table->unique(['batch_day_id', 'time']); // same day cannot have duplicate time
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_times');
    }
};
