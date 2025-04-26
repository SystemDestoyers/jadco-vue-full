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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general'); // Group (email, appearance, localization, etc)
            $table->string('key')->unique(); // Unique key
            $table->text('value')->nullable(); // Value stored as text (will be cast based on type)
            $table->string('type')->default('string'); // Type (string, boolean, integer, json, etc)
            $table->string('name'); // Human-readable name
            $table->text('description')->nullable(); // Description
            $table->boolean('is_public')->default(false); // Whether it can be accessed from public API
            $table->integer('order')->default(0); // For ordering in admin panel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
