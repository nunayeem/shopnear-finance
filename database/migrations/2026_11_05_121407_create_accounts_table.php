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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('parent_account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->string('account_code')->unique();

            $table->string('name');

            // asset, liability, equity, income, expense
            $table->string('account_type');

            // debit or credit
            $table->string('normal_balance');

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
