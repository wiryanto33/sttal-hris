<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Set default value for user_details.status to 'inactive'.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE user_details MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'inactive'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE user_details ALTER COLUMN status SET DEFAULT 'inactive'");
        } else {
            // For sqlite/sqlsrv or others, skip to avoid brittle ALTER syntax.
            // You can adjust per your DB engine if needed.
        }
    }

    /**
     * Revert the default value change.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE user_details MODIFY COLUMN status VARCHAR(255) NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE user_details ALTER COLUMN status DROP DEFAULT");
        } else {
            // Skip for unsupported engines in this migration.
        }
    }
};

