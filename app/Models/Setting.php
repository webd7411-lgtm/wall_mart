<?php

namespace App\Models;

use App\Helpers\BranchHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'key', 'value', 'type', 'group', 'label', 'description'];

    /**
     * Relationship to Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Resolve effective branch ID.
     */
    public static function resolveBranchId(?int $branchId = null): int
    {
        if ($branchId !== null && $branchId > 0) {
            return $branchId;
        }

        $active = BranchHelper::getActiveBranchId();
        if ($active !== null && $active > 0) {
            return $active;
        }

        return 1;
    }

    /**
     * Get a setting value by key (branch-aware with fallback).
     */
    public static function get(string $key, $default = null, ?int $branchId = null)
    {
        $targetBranchId = self::resolveBranchId($branchId);
        $cacheKey = 'setting_' . $key . '_branch_' . $targetBranchId;

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $targetBranchId) {
            // 1. Try finding setting for target branch
            $setting = self::where('key', $key)->where('branch_id', $targetBranchId)->first();

            // 2. If not found and target is not 1, fallback to branch 1
            if (!$setting && $targetBranchId !== 1) {
                $setting = self::where('key', $key)->where('branch_id', 1)->first();
            }

            // 3. Fallback to unassigned (null branch) if any exists
            if (!$setting) {
                $setting = self::where('key', $key)->whereNull('branch_id')->first();
            }

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value for a specific branch.
     */
    public static function set(string $key, $value, ?string $group = null, ?string $type = null, ?string $label = null, ?string $description = null, ?int $branchId = null): bool
    {
        $targetBranchId = self::resolveBranchId($branchId);

        $setting = self::where('key', $key)->where('branch_id', $targetBranchId)->first();

        if (!$setting) {
            $setting = new self();
            $setting->branch_id = $targetBranchId;
            $setting->key = $key;
            $setting->group = $group ?? 'company';
            $setting->type = $type ?? 'string';
            $setting->label = $label ?? ucwords(str_replace('_', ' ', $key));
            $setting->description = $description;
        }

        $setting->value = $value;
        $setting->save();

        // Invalidate branch-specific and generic caches
        Cache::forget('setting_' . $key);
        Cache::forget('setting_' . $key . '_branch_' . $targetBranchId);
        Cache::forget('setting_' . $key . '_branch_1');

        return true;
    }

    /**
     * Get all settings grouped by category for a specific branch.
     */
    public static function getAllGrouped(?int $branchId = null): array
    {
        $targetBranchId = self::resolveBranchId($branchId);

        // Fetch settings for this branch
        $branchSettings = self::where('branch_id', $targetBranchId)->get();

        // If target branch is missing any setting keys present in branch 1, copy them over so form has all options
        if ($targetBranchId !== 1) {
            $baseSettings = self::where('branch_id', 1)->get();
            $existingKeys = $branchSettings->pluck('key')->all();

            foreach ($baseSettings as $base) {
                if (!in_array($base->key, $existingKeys)) {
                    $cloned = self::create([
                        'branch_id' => $targetBranchId,
                        'key' => $base->key,
                        'value' => $base->value,
                        'type' => $base->type,
                        'group' => $base->group,
                        'label' => $base->label,
                        'description' => $base->description,
                    ]);
                    $branchSettings->push($cloned);
                }
            }
        }

        return $branchSettings->groupBy('group')->map(function ($settings) {
            return $settings->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => self::castValue($setting->value, $setting->type),
                    'type' => $setting->type,
                    'label' => $setting->label,
                    'description' => $setting->description,
                ];
            });
        })->toArray();
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
