<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('contacts')
            ->whereNotNull('role_codelist')
            ->update(['role_codelist' => null]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('role_codelist', 'jobs_codelist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('jobs_codelist', 'role_codelist');
        });
    }
};
