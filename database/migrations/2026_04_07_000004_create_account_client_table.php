<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('slot_label', 50)->nullable();
            $table->decimal('client_price', 8, 2);
            $table->string('currency', 3)->default('BOB');
            $table->smallInteger('payment_day');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['account_id', 'client_id', 'ended_at'], 'account_client_active_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_client');
    }
};
