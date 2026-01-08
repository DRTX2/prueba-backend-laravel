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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('tax_cost', 15, 2)->default(0);
            $table->decimal('manufacturing_cost', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
