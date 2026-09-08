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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('foyer_id')->constrained()->cascadeOnDelete();
            $table->date('start_day');
            $table->date('end_day');
            $table->timestamps();
        });

        Schema::create('absence_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('foyer_id')->constrained()->cascadeOnDelete();
            $table->date('start_day');
            $table->date('end_day');
            $table->enum('status', ['En attente', 'Réjété', 'Accepté'])->default('En attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::dropIfExists('absence_requests');
        schema::dropIfExists('absences');
    }
};
