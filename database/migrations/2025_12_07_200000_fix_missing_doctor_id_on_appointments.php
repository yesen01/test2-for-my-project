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
        if (! Schema::hasColumn('appointments', 'doctor_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                // place the column after user_id if possible
                $table->unsignedBigInteger('doctor_id')->nullable()->after('user_id');
            });

            // add foreign key in a separate statement (some DB drivers require it)
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('appointments', 'doctor_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                // drop foreign key if exists
                // name may vary depending on DB; attempt to drop by column
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $doctrineTable = $sm->listTableDetails(Schema::getConnection()->getTablePrefix() . 'appointments');
                if ($doctrineTable->hasForeignKey('appointments_doctor_id_foreign')) {
                    $table->dropForeign('appointments_doctor_id_foreign');
                }

                if (Schema::hasColumn('appointments', 'doctor_id')) {
                    $table->dropColumn('doctor_id');
                }
            });
        }
    }
};
