<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update employees platform from their interview's lead platform
        DB::statement("
            UPDATE employees e
            INNER JOIN interviews i ON e.email = i.candidate_email
            INNER JOIN leads l ON i.lead_id = l.id
            SET e.platform = l.platform
            WHERE e.platform IS NULL AND l.platform IS NOT NULL
        ");
    }

    public function down()
    {
        // No rollback needed
    }
};
