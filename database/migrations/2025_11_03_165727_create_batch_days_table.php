<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->string('days'); // e.g., "Sat-Mon-Wed"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_days');
    }
};
