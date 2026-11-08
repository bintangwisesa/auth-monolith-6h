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
        Schema::create('loans', function (Blueprint $table) {
    $table->id();
    $table->string('loan_number')->unique();
    $table->unsignedBigInteger('member_id')->index();
    $table->string('status')->default('borrowed'); // borrowed | returned
    $table->timestamp('borrowed_at')->useCurrent();
    $table->date('due_date');
    $table->timestamp('returned_at')->nullable();
    $table->decimal('fine_amount', 12, 2)->default(0);
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
