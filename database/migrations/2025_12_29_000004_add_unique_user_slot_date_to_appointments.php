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
        Schema::table('appointments', function (Blueprint $table) {
            // add unique index to prevent same user booking the same slot on same date
            if (!Schema::hasColumn('appointments', 'doctor_slot_id')) {
                // nothing to do
                return;
            }

            // only add if not exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = array_map('strtolower', array_keys($sm->listTableIndexes('appointments')));
            if (!in_array('appointments_user_slot_date_unique', $indexes)) {
                $table->unique(['user_id','doctor_slot_id','date'], 'appointments_user_slot_date_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('appointments_user_slot_date_unique');
        });
    }
};
