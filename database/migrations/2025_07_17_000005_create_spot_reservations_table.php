<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('spot_reservations', static function (Blueprint $table) {
            $table->uuid('spot_id')->primary();
            $table->date('reservation_date');
            $table->uuid('customer_id');
            $table->timestamps();

            $table->foreign('spot_id')
                ->references('id')
                ->on('event_spots')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sections');
    }
};
