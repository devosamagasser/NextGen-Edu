<?php

namespace Database\Seeders;

use App\Models\CourseDetail;
use Illuminate\Database\Seeder;

class CourseDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['course_id' => '5','department_id' => 12, 'semester_id' => 3], // ميكانيكا التربة
            ['course_id' => '6', 'department_id' => 12, 'semester_id' => 4], // خرسانة مسلحة
            ['course_id' => '7', 'department_id' => 12, 'semester_id' => 4], // تحليل إنشائي
            ['course_id' => '8', 'department_id' => 12, 'semester_id' => 5], // هندسة الأساسات
            ['course_id' => '9', 'department_id' => 12, 'semester_id' => 5], // تصميم منشآت معدنية
            ['course_id' => '10', 'department_id' => 12, 'semester_id' => 6], // هندسة مرور
            ['course_id' => '11', 'department_id' => 12, 'semester_id' => 7], // هندسة بيئية
            ['course_id' => '12', 'department_id' => 12, 'semester_id' => 3], // مساحة
            ['course_id' => '13', 'department_id' => 12, 'semester_id' => 6], // هندسة رى وصرف
            
            ['course_id' => '14', 'department_id' => 18, 'semester_id' => 1], // تصميم معماري 1
            ['course_id' => '15', 'department_id' => 18, 'semester_id' => 2], // تصميم معماري 2
            ['course_id' => '16', 'department_id' => 18, 'semester_id' => 3], // تاريخ العمارة
            ['course_id' => '17', 'department_id' => 18, 'semester_id' => 4], // نظريات العمارة
            ['course_id' => '18', 'department_id' => 18, 'semester_id' => 5], // الإنشاء المعماري
            ['course_id' => '19', 'department_id' => 18, 'semester_id' => 6], // رسم معماري
            ['course_id' => '20', 'department_id' => 18, 'semester_id' => 7], // تخطيط عمراني
            
            ['course_id' => '21', 'department_id' => 13, 'semester_id' => 3], // ميكانيكا الموائع
            ['course_id' => '22', 'department_id' => 13, 'semester_id' => 4], // ديناميكا حرارية
            ['course_id' => '23', 'department_id' => 13, 'semester_id' => 5], // تصميم ماكينات
            ['course_id' => '24', 'department_id' => 13, 'semester_id' => 5], // انتقال حرارة
            ['course_id' => '25', 'department_id' => 13, 'semester_id' => 6], // محطات قوى
            ['course_id' => '26', 'department_id' => 13, 'semester_id' => 7], // التحكم الآلي
            ['course_id' => '27', 'department_id' => 13, 'semester_id' => 7], // نظم هيدروليكية
            ['course_id' => '28', 'department_id' => 13, 'semester_id' => 6], // ميكانيكا المواد
            
            ['course_id' => '29', 'department_id' => 15, 'semester_id' => 2], // دوائر كهربائية
            ['course_id' => '30', 'department_id' => 15, 'semester_id' => 3], // دوائر إلكترونية
            ['course_id' => '31', 'department_id' => 15, 'semester_id' => 4], // اتصالات تماثلية
            ['course_id' => '32', 'department_id' => 15, 'semester_id' => 5], // اتصالات رقمية
            ['course_id' => '33', 'department_id' => 15, 'semester_id' => 6], // نظم التحكم
            ['course_id' => '34', 'department_id' => 15, 'semester_id' => 7], // ميكروويف
            ['course_id' => '35', 'department_id' => 15, 'semester_id' => 7], // هوائيات
            ['course_id' => '36', 'department_id' => 15, 'semester_id' => 8], // معالجة إشارات
            
            ['course_id' => '37', 'department_id' => 11, 'semester_id' => 1], // برمجة
            ['course_id' => '38', 'department_id' => 11, 'semester_id' => 2], // هياكل بيانات
            ['course_id' => '39', 'department_id' => 11, 'semester_id' => 3], // أنظمة تشغيل
            ['course_id' => '40', 'department_id' => 11, 'semester_id' => 4], // قواعد بيانات
            ['course_id' => '41', 'department_id' => 11, 'semester_id' => 5], // شبكات
            ['course_id' => '42', 'department_id' => 11, 'semester_id' => 6], // هندسة برمجيات
            ['course_id' => '43', 'department_id' => 11, 'semester_id' => 7], // ذكاء اصطناعي
            ['course_id' => '44', 'department_id' => 11, 'semester_id' => 8], // نظم مدمجة
            ['course_id' => '45', 'department_id' => 11, 'semester_id' => 9], // أمن معلومات
            
            ['course_id' => '46', 'department_id' => 16, 'semester_id' => 4], // آلات كهربائية
            ['course_id' => '47', 'department_id' => 16, 'semester_id' => 5], // نظم قوى
            ['course_id' => '48', 'department_id' => 16, 'semester_id' => 6], // تحليل نظم
            ['course_id' => '49', 'department_id' => 16, 'semester_id' => 6], // مكونات إلكترونية
            ['course_id' => '50', 'department_id' => 16, 'semester_id' => 7], // إلكترونيات قدرة
            ['course_id' => '51', 'department_id' => 16, 'semester_id' => 8], // توزيع كهربائي
            ['course_id' => '52', 'department_id' => 16, 'semester_id' => 9], // تحكم كهربائي
        ];

    
        CourseDetail::insert($data);
    }


    // $data = [
    //     ['course_id' => 1, 'department_id' => 3, 'semester_id' => 3], // ميكانيكا التربة
    //     ['course_id' => 2, 'department_id' => 3, 'semester_id' => 4], // خرسانة مسلحة
    //     ['course_id' => 3, 'department_id' => 3, 'semester_id' => 4], // تحليل إنشائي
    //     ['course_id' => 4, 'department_id' => 3, 'semester_id' => 5], // هندسة الأساسات
    //     ['course_id' => 5, 'department_id' => 3, 'semester_id' => 5], // تصميم منشآت معدنية
    //     ['course_id' => 6, 'department_id' => 3, 'semester_id' => 6], // هندسة مرور
    //     ['course_id' => 7, 'department_id' => 3, 'semester_id' => 7], // هندسة بيئية
    //     ['course_id' => 8, 'department_id' => 3, 'semester_id' => 3], // مساحة
    //     ['course_id' => 9, 'department_id' => 3, 'semester_id' => 6], // هندسة ري وصرف
    
    //     ['course_id' => 10, 'department_id' => 9, 'semester_id' => 1], // تصميم معماري 1
    //     ['course_id' => 11, 'department_id' => 9, 'semester_id' => 2], // تصميم معماري 2
    //     ['course_id' => 12, 'department_id' => 9, 'semester_id' => 3], // تاريخ العمارة
    //     ['course_id' => 13, 'department_id' => 9, 'semester_id' => 4], // نظريات العمارة
    //     ['course_id' => 14, 'department_id' => 9, 'semester_id' => 5], // الإنشاء المعماري
    //     ['course_id' => 15, 'department_id' => 9, 'semester_id' => 6], // رسم معماري
    //     ['course_id' => 16, 'department_id' => 9, 'semester_id' => 7], // تخطيط عمراني
    
    //     ['course_id' => 17, 'department_id' => 4, 'semester_id' => 3], // ميكانيكا الموائع
    //     ['course_id' => 18, 'department_id' => 4, 'semester_id' => 4], // ديناميكا حرارية
    //     ['course_id' => 19, 'department_id' => 4, 'semester_id' => 5], // تصميم ماكينات
    //     ['course_id' => 20, 'department_id' => 4, 'semester_id' => 5], // انتقال حرارة
    //     ['course_id' => 21, 'department_id' => 4, 'semester_id' => 6], // محطات قوى
    //     ['course_id' => 22, 'department_id' => 4, 'semester_id' => 7], // التحكم الآلي
    //     ['course_id' => 23, 'department_id' => 4, 'semester_id' => 7], // نظم هيدروليكية
    //     ['course_id' => 24, 'department_id' => 4, 'semester_id' => 6], // ميكانيكا المواد
    
    //     ['course_id' => 25, 'department_id' => 6, 'semester_id' => 2], // دوائر كهربائية
    //     ['course_id' => 26, 'department_id' => 6, 'semester_id' => 3], // دوائر إلكترونية
    //     ['course_id' => 27, 'department_id' => 6, 'semester_id' => 4], // اتصالات تماثلية
    //     ['course_id' => 28, 'department_id' => 6, 'semester_id' => 5], // اتصالات رقمية
    //     ['course_id' => 29, 'department_id' => 6, 'semester_id' => 6], // نظم التحكم
    //     ['course_id' => 30, 'department_id' => 6, 'semester_id' => 7], // ميكروويف
    //     ['course_id' => 31, 'department_id' => 6, 'semester_id' => 7], // هوائيات
    //     ['course_id' => 32, 'department_id' => 6, 'semester_id' => 8], // معالجة إشارات
    
    //     ['course_id' => 33, 'department_id' => 2, 'semester_id' => 1], // برمجة
    //     ['course_id' => 34, 'department_id' => 2, 'semester_id' => 2], // هياكل بيانات
    //     ['course_id' => 35, 'department_id' => 2, 'semester_id' => 3], // أنظمة تشغيل
    //     ['course_id' => 36, 'department_id' => 2, 'semester_id' => 4], // قواعد بيانات
    //     ['course_id' => 37, 'department_id' => 2, 'semester_id' => 5], // شبكات
    //     ['course_id' => 38, 'department_id' => 2, 'semester_id' => 6], // هندسة برمجيات
    //     ['course_id' => 39, 'department_id' => 2, 'semester_id' => 7], // ذكاء اصطناعي
    //     ['course_id' => 40, 'department_id' => 2, 'semester_id' => 8], // نظم مدمجة
    //     ['course_id' => 41, 'department_id' => 2, 'semester_id' => 9], // أمن معلومات
    
    //     ['course_id' => 42, 'department_id' => 7, 'semester_id' => 4], // آلات كهربائية
    //     ['course_id' => 43, 'department_id' => 7, 'semester_id' => 5], // نظم قوى
    //     ['course_id' => 44, 'department_id' => 7, 'semester_id' => 6], // تحليل نظم
    //     ['course_id' => 45, 'department_id' => 7, 'semester_id' => 6], // مكونات إلكترونية
    //     ['course_id' => 46, 'department_id' => 7, 'semester_id' => 7], // إلكترونيات قدرة
    //     ['course_id' => 47, 'department_id' => 7, 'semester_id' => 8], // توزيع كهربائي
    //     ['course_id' => 48, 'department_id' => 7, 'semester_id' => 9], // تحكم كهربائي
    // ];
    
}
