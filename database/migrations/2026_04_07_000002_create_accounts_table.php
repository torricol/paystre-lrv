<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streaming_service_id')->constrained()->cascadeOnDelete();
            $table->string('label', 100);
            $table->string('email', 255);
            $table->text('password');
            $table->string('pin', 255)->nullable();
            $table->text('extra_credentials')->nullable();
            $table->string('plan_name', 100)->nullable();
            $table->decimal('cost', 8, 2);
            $table->string('currency', 3)->default('BOB');
            $table->smallInteger('billing_day');
            $table->date('next_billing_date');
            $table->smallInteger('max_slots')->default(5);
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
