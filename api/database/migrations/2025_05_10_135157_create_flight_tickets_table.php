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
        Schema::create('flight_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique(); // id do pedido
            $table->unsignedBigInteger('user_id'); // id user
            $table->unsignedBigInteger('requester_id'); // id do solicitante
            $table->string('origin'); // origem
            $table->string('destination'); // destino
            $table->date('departure_date'); // data da ida
            $table->date('return_date')->nullable(); // data da volta (pode ser nulo)
            $table->enum('status', ['solicitado', 'aprovado', 'cancelado'])->default('solicitado'); // status
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('requester_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_tickets');
    }
};
