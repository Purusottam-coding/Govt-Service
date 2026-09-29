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
        Schema::table('application_documents', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('file_path'); // pending, verified, replacement_needed
            $table->text('admin_feedback')->nullable()->after('status');
            $table->timestamp('replaced_at')->nullable()->after('admin_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_feedback', 'replaced_at']);
        });
    }
};
