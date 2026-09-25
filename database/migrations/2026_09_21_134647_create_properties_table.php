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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 255)->unique();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->default('San Miguel de Allende');
            $table->string('state', 100)->default('Guanajuato');
            $table->string('country', 100)->default('Mexico');
            $table->string('zip', 10);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sold')->default(false);
            $table->date('sold_date')->nullable();
            $table->decimal('price_usd', 15, 2);
            $table->decimal('price_mxn', 15, 2);
            $table->boolean('show_both_prices')->default(false);
            $table->longText('description')->nullable();
            $table->longText('description_es')->nullable();
            $table->float('lot_meters');
            $table->float('construction_meters');
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('half_bathrooms')->default(0);
            $table->integer('parking_spaces')->default(0);
            $table->longText('notes')->nullable();
            $table->integer('property_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
