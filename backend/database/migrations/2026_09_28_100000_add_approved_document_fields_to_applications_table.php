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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('approved_document_path')->nullable()->after('admin_remarks');
            $table->string('approved_document_name')->nullable()->after('approved_document_path');
            $table->string('approved_document_type', 20)->nullable()->after('approved_document_name');
            $table->string('certificate_number', 50)->nullable()->index()->after('approved_document_type');
            $table->timestamp('issued_at')->nullable()->after('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'approved_document_path',
                'approved_document_name',
                'approved_document_type',
                'certificate_number',
                'issued_at',
            ]);
        });
    }
};
