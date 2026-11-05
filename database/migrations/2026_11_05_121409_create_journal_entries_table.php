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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();

            $table->string('entry_number')->unique();

            // investment, income, expense, withdrawal,
            // transfer, adjustment, distribution
            $table->string('entry_type');

            $table->date('transaction_date');

            $table->string('reference_number')->nullable();
            $table->string('invoice_path')->nullable();

            $table->string('description')->nullable();

            // draft, posted, void
            $table->string('status')->default('draft');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('posted_at')->nullable();

            $table->timestamps();

            $table->index(['entry_type', 'status']);
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
