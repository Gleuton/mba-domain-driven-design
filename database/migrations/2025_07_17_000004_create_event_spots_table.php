<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_spots', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('location', 255)->nullable();
            $table->boolean('is_reserved')->default(false);
            $table->boolean('is_published')->default(false);
            $table->uuid('event_section_id');
            $table->timestamps();

            $table->foreign('event_section_id')
                ->references('id')
                ->on('event_sections')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sections');
    }
};
