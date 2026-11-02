<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teamForeignKey = $columnNames['team_foreign_key'] ?? 'branch_id';

        // 1. Update roles table
        if (Schema::hasTable($tableNames['roles'])) {
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamForeignKey) {
                if (!Schema::hasColumn($table->getTable(), $teamForeignKey)) {
                    $table->unsignedBigInteger($teamForeignKey)->nullable()->after('id');
                    $table->index($teamForeignKey, 'roles_branch_id_index');
                }
            });

            // Drop old unique index if exists
            try {
                DB::statement("ALTER TABLE `{$tableNames['roles']}` DROP INDEX `roles_name_guard_name_unique`");
            } catch (\Exception $e) {}

            // Add compound unique index for branch-scoped roles
            try {
                DB::statement("ALTER TABLE `{$tableNames['roles']}` ADD UNIQUE INDEX `roles_branch_name_guard_unique` (`{$teamForeignKey}`, `name`, `guard_name`)");
            } catch (\Exception $e) {}
        }

        // 2. Update model_has_roles table
        if (Schema::hasTable($tableNames['model_has_roles'])) {
            // Check if branch_id column exists
            if (!Schema::hasColumn($tableNames['model_has_roles'], $teamForeignKey)) {
                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` DROP PRIMARY KEY");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` ADD `{$teamForeignKey}` BIGINT UNSIGNED NULL AFTER `id`");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` ADD INDEX `model_has_roles_branch_id_index` (`{$teamForeignKey}`)");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` ADD UNIQUE KEY `model_has_roles_branch_role_model_unique` (`{$teamForeignKey}`, `role_id`, `model_id`, `model_type`)");
                } catch (\Exception $e) {}
            }
        }

        // 3. Update model_has_permissions table
        if (Schema::hasTable($tableNames['model_has_permissions'])) {
            if (!Schema::hasColumn($tableNames['model_has_permissions'], $teamForeignKey)) {
                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` DROP PRIMARY KEY");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` ADD `{$teamForeignKey}` BIGINT UNSIGNED NULL AFTER `id`");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` ADD INDEX `model_has_permissions_branch_id_index` (`{$teamForeignKey}`)");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` ADD UNIQUE KEY `model_has_permissions_branch_perm_model_unique` (`{$teamForeignKey}`, `permission_id`, `model_id`, `model_type`)");
                } catch (\Exception $e) {}
            }
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe reversible logic if ever rolled back
    }
};
