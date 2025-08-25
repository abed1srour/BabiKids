<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('activity_child')) {
            Schema::create('activity_child', function (Blueprint $table) {
                $table->id();
                $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
                $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
                $table->foreignId('recorded_by')->nullable()->constrained('staff')->nullOnDelete();
                $table->enum('status', ['planned','completed','missed'])->default('planned');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['activity_id', 'child_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_child');
    }
};
