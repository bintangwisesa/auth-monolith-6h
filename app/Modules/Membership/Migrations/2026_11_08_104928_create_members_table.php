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
        Schema::create('members', function (Blueprint $table) {
    $table->id();
    $table->string('member_number')->unique();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('status')->default('active'); // active | borrowing | suspended
    $table->unsignedInteger('max_loans')->default(3);
    $table->unsignedInteger('active_loans')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
