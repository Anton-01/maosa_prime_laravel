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
        Schema::create('listing_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('social_network_id')->constrained('social_networks')->onDelete('cascade');
            $table->text('url'); // The URL for this social network
            $table->integer('order')->default(0); // Display order
            $table->timestamps();

            // Ensure a listing can't have duplicate entries for the same social network
            $table->unique(['listing_id', 'social_network_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_social_links');
    }
};
