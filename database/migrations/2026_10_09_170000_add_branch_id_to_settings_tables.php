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
        // 1. Modify settings table
        if (Schema::hasTable('settings')) {
            if (!Schema::hasColumn('settings', 'branch_id')) {
                Schema::table('settings', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });
            }

            // Drop old unique constraint on key if it exists
            try {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropUnique('settings_key_unique');
                });
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `settings` DROP INDEX `settings_key_unique`");
                } catch (\Throwable $e2) {}
            }

            try {
                Schema::table('settings', function (Blueprint $table) {
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                });
            } catch (\Throwable $e) {}

            // Populate existing settings for each existing branch
            $existingSettings = DB::table('settings')->whereNull('branch_id')->get();
            $branches = DB::table('branches')->get();

            if ($branches->isNotEmpty()) {
                $firstBranch = $branches->first();

                // Assign existing unassigned rows to branch 1
                if ($existingSettings->isNotEmpty()) {
                    DB::table('settings')->whereNull('branch_id')->update(['branch_id' => $firstBranch->id]);
                }

                // Re-fetch branch 1 settings to clone for other branches
                $baseSettings = DB::table('settings')->where('branch_id', $firstBranch->id)->get();

                // Clone settings for other branches so each branch has its own records
                foreach ($branches->slice(1) as $branch) {
                    foreach ($baseSettings as $item) {
                        $exists = DB::table('settings')
                            ->where('key', $item->key)
                            ->where('branch_id', $branch->id)
                            ->exists();

                        if (!$exists) {
                            $companyName = $item->value;
                            if ($item->key === 'company_name' && !empty($item->value)) {
                                $companyName = $branch->name;
                            }

                            DB::table('settings')->insert([
                                'branch_id' => $branch->id,
                                'key' => $item->key,
                                'value' => $companyName,
                                'type' => $item->type,
                                'group' => $item->group,
                                'label' => $item->label,
                                'description' => $item->description,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Add composite unique index if not present
            try {
                Schema::table('settings', function (Blueprint $table) {
                    $table->unique(['branch_id', 'key'], 'settings_branch_key_unique');
                });
            } catch (\Throwable $e) {}
        }

        // 2. Modify system_settings table if it exists
        if (Schema::hasTable('system_settings')) {
            if (!Schema::hasColumn('system_settings', 'branch_id')) {
                Schema::table('system_settings', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });
            }

            try {
                Schema::table('system_settings', function (Blueprint $table) {
                    $table->dropUnique('system_settings_key_unique');
                });
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `system_settings` DROP INDEX `system_settings_key_unique`");
                } catch (\Throwable $e2) {}
            }

            try {
                Schema::table('system_settings', function (Blueprint $table) {
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                });
            } catch (\Throwable $e) {}

            $branches = DB::table('branches')->get();
            $existingSysSettings = DB::table('system_settings')->whereNull('branch_id')->get();

            if ($branches->isNotEmpty()) {
                $firstBranch = $branches->first();
                if ($existingSysSettings->isNotEmpty()) {
                    DB::table('system_settings')->whereNull('branch_id')->update(['branch_id' => $firstBranch->id]);
                }

                $baseSysSettings = DB::table('system_settings')->where('branch_id', $firstBranch->id)->get();

                foreach ($branches->slice(1) as $branch) {
                    foreach ($baseSysSettings as $item) {
                        $exists = DB::table('system_settings')
                            ->where('key', $item->key)
                            ->where('branch_id', $branch->id)
                            ->exists();

                        if (!$exists) {
                            DB::table('system_settings')->insert([
                                'branch_id' => $branch->id,
                                'key' => $item->key,
                                'value' => $item->value,
                                'type' => $item->type,
                                'group' => $item->group,
                                'label' => $item->label,
                                'description' => $item->description,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            try {
                Schema::table('system_settings', function (Blueprint $table) {
                    $table->unique(['branch_id', 'key'], 'system_settings_branch_key_unique');
                });
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'branch_id')) {
            Schema::table('settings', function (Blueprint $table) {
                try {
                    $table->dropUnique('settings_branch_key_unique');
                } catch (\Throwable $e) {}
                try {
                    $table->dropForeign(['branch_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('branch_id');
                try {
                    $table->unique('key', 'settings_key_unique');
                } catch (\Throwable $e) {}
            });
        }

        if (Schema::hasTable('system_settings') && Schema::hasColumn('system_settings', 'branch_id')) {
            Schema::table('system_settings', function (Blueprint $table) {
                try {
                    $table->dropUnique('system_settings_branch_key_unique');
                } catch (\Throwable $e) {}
                try {
                    $table->dropForeign(['branch_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('branch_id');
                try {
                    $table->unique('key', 'system_settings_key_unique');
                } catch (\Throwable $e) {}
            });
        }
    }
};
