<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_client', function (Blueprint $table) {
            $table->string('pin', 255)->nullable()->after('slot_label');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('pin', 255)->nullable()->after('password');
        });

        Schema::table('account_client', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
