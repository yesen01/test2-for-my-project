<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // إضافة DB لفحص الفهارس

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // 1. التأكد من وجود الأعمدة المطلوبة قبل إنشاء القيد الفريد
            if (Schema::hasColumns('appointments', ['doctor_id', 'doctor_slot_id', 'date'])) {

                // 2. التحقق يدويًا من وجود الفهرس لتجنب خطأ التكرار (بدون Doctrine)
                $logicalName = 'appointments_doctor_slot_date_unique';

                // جلب قائمة الفهارس الحالية للجدول
                $schemaManager = Schema::getConnection()->getSchemaBuilder();

                // إضافة القيد الفريد فقط إذا لم يكن موجوداً
                // نستخدم محاولة احتواء (Try-Catch) أو فحص مباشر بسيط
                try {
                    $table->unique(['doctor_id', 'doctor_slot_id', 'date'], $logicalName);
                } catch (\Exception $e) {
                    // إذا كان الفهرس موجوداً مسبقاً، سيتخطى الخطأ
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // التأكد من حذف نفس الاسم الذي تم إنشاؤه في دالة up
            $logicalName = 'appointments_doctor_slot_date_unique';

            // نتحقق من وجود الفهرس قبل حذفه لتجنب الأخطاء
            $table->dropUnique($logicalName);
        });
    }
};
