<?php

use App\Models\User;
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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('photo', 2048)->nullable();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->longText('bio')->nullable();
            $table->longText('bio_es')->nullable();
            $table->integer('agent_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
