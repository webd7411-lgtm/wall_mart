<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $teamForeignKey = config('permission.column_names.team_foreign_key', 'branch_id');

        // 1. model_has_roles
        if (!Schema::hasColumn('model_has_roles', $teamForeignKey)) {
            // First drop foreign key on role_id so primary key can be safely modified
            DB::statement("ALTER TABLE `model_has_roles` DROP FOREIGN KEY `model_has_roles_role_id_foreign`");
            DB::statement("ALTER TABLE `model_has_roles` DROP PRIMARY KEY");
            DB::statement("ALTER TABLE `model_has_roles` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
            DB::statement("ALTER TABLE `model_has_roles` ADD `{$teamForeignKey}` BIGINT UNSIGNED NULL AFTER `id`");
            DB::statement("ALTER TABLE `model_has_roles` ADD INDEX `model_has_roles_branch_id_index` (`{$teamForeignKey}`)");
            DB::statement("ALTER TABLE `model_has_roles` ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE");
            DB::statement("ALTER TABLE `model_has_roles` ADD UNIQUE KEY `model_has_roles_branch_role_model_unique` (`{$teamForeignKey}`, `role_id`, `model_id`, `model_type`)");
        }

        // 2. model_has_permissions
        if (!Schema::hasColumn('model_has_permissions', $teamForeignKey)) {
            DB::statement("ALTER TABLE `model_has_permissions` DROP FOREIGN KEY `model_has_permissions_permission_id_foreign`");
            DB::statement("ALTER TABLE `model_has_permissions` DROP PRIMARY KEY");
            DB::statement("ALTER TABLE `model_has_permissions` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
            DB::statement("ALTER TABLE `model_has_permissions` ADD `{$teamForeignKey}` BIGINT UNSIGNED NULL AFTER `id`");
            DB::statement("ALTER TABLE `model_has_permissions` ADD INDEX `model_has_permissions_branch_id_index` (`{$teamForeignKey}`)");
            DB::statement("ALTER TABLE `model_has_permissions` ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE");
            DB::statement("ALTER TABLE `model_has_permissions` ADD UNIQUE KEY `model_has_permissions_branch_perm_model_unique` (`{$teamForeignKey}`, `permission_id`, `model_id`, `model_type`)");
        }
    }

    public function down(): void
    {
    }
};
