
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Drop FKs to allow modifying nullability, then re-add
            DB::statement('ALTER TABLE user_details DROP FOREIGN KEY user_details_departement_id_foreign');
            DB::statement('ALTER TABLE user_details DROP FOREIGN KEY user_details_role_id_foreign');

            DB::statement("ALTER TABLE user_details
                MODIFY pangkat VARCHAR(255) NULL,
                MODIFY korps VARCHAR(255) NULL,
                MODIFY nrp VARCHAR(255) NULL,
                MODIFY gender VARCHAR(255) NULL,
                MODIFY address VARCHAR(255) NULL,
                MODIFY phone VARCHAR(255) NULL,
                MODIFY birth_date DATE NULL,
                MODIFY join_date DATE NULL,
                MODIFY departement_id BIGINT UNSIGNED NULL,
                MODIFY role_id BIGINT UNSIGNED NULL
            ");

            DB::statement('ALTER TABLE user_details ADD CONSTRAINT user_details_departement_id_foreign FOREIGN KEY (departement_id) REFERENCES departements(id)');
            DB::statement('ALTER TABLE user_details ADD CONSTRAINT user_details_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles(id)');
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE user_details
                ALTER COLUMN pangkat DROP NOT NULL,
                ALTER COLUMN korps DROP NOT NULL,
                ALTER COLUMN nrp DROP NOT NULL,
                ALTER COLUMN gender DROP NOT NULL,
                ALTER COLUMN address DROP NOT NULL,
                ALTER COLUMN phone DROP NOT NULL,
                ALTER COLUMN birth_date DROP NOT NULL,
                ALTER COLUMN join_date DROP NOT NULL,
                ALTER COLUMN departement_id DROP NOT NULL,
                ALTER COLUMN role_id DROP NOT NULL
            ");
        } else {
            // Skip for unsupported engines (sqlite/sqlsrv) to avoid brittle syntax
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE user_details DROP FOREIGN KEY user_details_departement_id_foreign');
            DB::statement('ALTER TABLE user_details DROP FOREIGN KEY user_details_role_id_foreign');

            DB::statement("ALTER TABLE user_details
                MODIFY pangkat VARCHAR(255) NOT NULL,
                MODIFY korps VARCHAR(255) NOT NULL,
                MODIFY nrp VARCHAR(255) NOT NULL,
                MODIFY gender VARCHAR(255) NOT NULL,
                MODIFY address VARCHAR(255) NOT NULL,
                MODIFY phone VARCHAR(255) NOT NULL,
                MODIFY birth_date DATE NOT NULL,
                MODIFY join_date DATE NOT NULL,
                MODIFY departement_id BIGINT UNSIGNED NOT NULL,
                MODIFY role_id BIGINT UNSIGNED NOT NULL
            ");

            DB::statement('ALTER TABLE user_details ADD CONSTRAINT user_details_departement_id_foreign FOREIGN KEY (departement_id) REFERENCES departements(id)');
            DB::statement('ALTER TABLE user_details ADD CONSTRAINT user_details_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles(id)');
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE user_details
                ALTER COLUMN pangkat SET NOT NULL,
                ALTER COLUMN korps SET NOT NULL,
                ALTER COLUMN nrp SET NOT NULL,
                ALTER COLUMN gender SET NOT NULL,
                ALTER COLUMN address SET NOT NULL,
                ALTER COLUMN phone SET NOT NULL,
                ALTER COLUMN birth_date SET NOT NULL,
                ALTER COLUMN join_date SET NOT NULL,
                ALTER COLUMN departement_id SET NOT NULL,
                ALTER COLUMN role_id SET NOT NULL
            ");
        } else {
            // Skip for unsupported engines
        }
    }
};

