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
        Schema::table('countries', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->comment('-1 => All, 0 => Deleted, 1 => Active, 2 => Checker, 3 => Approver, 4 => Rejected, 5 => Issue Raised, 6 => Deactive')->after('STD_code');
            $table->string('reason_to_delete')->nullable()->after('status');
            $table->integer('created_by')->unsigned()->nullable()->after('reason_to_delete');
            $table->integer('updated_by')->unsigned()->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('reason_to_delete');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};
