<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update employees platform from interested_candidates if still NULL
        DB::statement("
            UPDATE employees e
            INNER JOIN interested_candidates ic ON e.email = ic.email
            SET e.platform = ic.platform
            WHERE e.platform IS NULL AND ic.platform IS NOT NULL
        ");
    }

    public function down()
    {
        // No rollback needed
    }
};
