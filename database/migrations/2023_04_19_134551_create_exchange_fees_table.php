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
        Schema::create('exchange_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exchange_request_id')
                ->constrained('exchange_requests');
            $table->string('currency');
            $table->decimal('fee', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_fees');
    }
};
