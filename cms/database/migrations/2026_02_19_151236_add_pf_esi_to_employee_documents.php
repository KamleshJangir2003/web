<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE employee_documents MODIFY COLUMN document_type ENUM(
            'aadhar_card',
            'pan_card', 
            'photo',
            'marksheet_10th',
            'marksheet_12th',
            'graduation',
            'diploma',
            'post_graduation',
            'passbook',
            'cheque',
            'pf_esi',
            'bank_statement',
            'experience_letter',
            'joining_letter',
            'salary_slips'
        )");
    }

    public function down()
    {
        DB::statement("ALTER TABLE employee_documents MODIFY COLUMN document_type ENUM(
            'aadhar_card',
            'pan_card',
            'photo',
            'marksheet_10th',
            'marksheet_12th',
            'graduation',
            'diploma',
            'post_graduation',
            'passbook',
            'cheque',
            'bank_statement',
            'experience_letter',
            'joining_letter',
            'salary_slips'
        )");
    }
};
