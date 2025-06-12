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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();

            $table->string('pangkat');
            $table->string('korps');
            $table->string('nrp')->unique();
            $table->string('gender');
            $table->string('image')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->date('birth_date');
            $table->date('join_date');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('departement_id')->constrained('departements');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('status');
            $table->decimal('salary', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
