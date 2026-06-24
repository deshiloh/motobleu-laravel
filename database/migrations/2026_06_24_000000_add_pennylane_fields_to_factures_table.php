<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->string('pennylane_invoice_id')->nullable()->after('billed_at');
            $table->string('pennylane_invoice_number')->nullable()->after('pennylane_invoice_id');
            $table->string('pennylane_customer_id')->nullable()->after('pennylane_invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(['pennylane_invoice_id', 'pennylane_invoice_number', 'pennylane_customer_id']);
        });
    }
};
