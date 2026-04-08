<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_client_id')->constrained('account_client')->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('BOB');
            $table->smallInteger('period_month');
            $table->smallInteger('period_year');
            $table->dateTime('paid_at');
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['account_client_id', 'period_month', 'period_year'], 'payments_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
