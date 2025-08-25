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
        Schema::create('attendance', function (Blueprint $table) {
$table->id();
$table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
$table->foreignId('recorded_by')->constrained('staff')->cascadeOnDelete();
$table->date('date');
$table->enum('status',['present','absent','late','excused']);
$table->time('check_in_time')->nullable();
$table->time('check_out_time')->nullable();
$table->text('notes')->nullable();
$table->timestamps();
$table->unique(['child_id','date']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
