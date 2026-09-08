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
        Schema::create('factures', function(Blueprint $table){
            $table->id();
            $table->foreignId('foyer_id')->constrained()->cascadeOnDelete();
            $table->decimal('eau', 8,2);
            $table->decimal('electricite', 8,2);
            $table->decimal('prix_moyen', 8,2);
            $table->decimal('total', 8,2);
            $table->decimal('consomation', 8,2);
            $table->enum('status', ['payé', 'non payé'])->default('non payé');
            $table->date('periode');
            $table->unique(['foyer_id', 'periode']);
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('repartitions', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facture_id')->constrained()->cascadeOnDelete();
            $table->float('part_commun');
            $table->float('part_appareil');
            $table->float('coef_presence');
            $table->decimal('total');
            $table->timestamps();
        });

        Schema::create('payements', function(Blueprint $table){
            $table->id();
            $table->foreignId('repartition_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8,2);
            $table->dateTime('paye_le');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::dropIfExists('repartitions');
        schema::dropIfExists('factures');
        schema::dropIfExists('payements');
    }
};
