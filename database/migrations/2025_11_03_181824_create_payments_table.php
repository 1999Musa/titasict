<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['admission', 'monthly']);
            $table->string('month')->nullable(); // For monthly payments, store month name like "October 2025"
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('Paid'); // Can extend later for Pending, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
