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
        Schema::create('payments', function (Blueprint $table) {
$table->id();
$table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
$table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
$table->decimal('amount',10,2);
$table->string('currency',8)->default('USD');
$table->enum('method',['cash','card','bank']);
$table->enum('status',['pending','paid','failed','refunded'])->default('pending');
$table->date('due_date')->nullable();
$table->timestamp('paid_at')->nullable();
$table->string('reference')->nullable();
$table->text('notes')->nullable();
$table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
