<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    if (!Schema::hasColumn('appointments', 'doctor_id')) {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained()->after('id')->onDelete('set null');
        });
    }
}


public function down()
{
    Schema::table('appointments', function (Blueprint $table) {
        $table->dropColumn('doctor_id');
    });
}

};
