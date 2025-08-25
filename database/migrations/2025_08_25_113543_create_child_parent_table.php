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
        Schema::create('child_parent', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
                $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
                $table->enum('relationship',['mother','father','guardian','other'])->nullable();
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_emergency_contact')->default(false);
                $table->timestamps();
                $table->unique(['child_id','parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_parent');
    }
};
