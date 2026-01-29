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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->decimal('deal_price', 10, 2);
            $table->boolean('status');
            $table->boolean('stock');
            $table->decimal('rating', 10, 1)->default(0);
            $table->string('cover')->nullable();
            $table->text('description')->nullable();
            $table->boolean('prescription')->nullable();
            $table->string('mfg');
            $table->string('packSize')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
