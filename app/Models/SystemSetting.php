<?php

namespace App\Models;

use App\Helpers\BranchHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'key', 'value', 'type', 'group', 'label', 'description'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    protected static function resolveBranchId(?int $branchId = null): int
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
     * Get a setting value by key
     */
    public static function get($key, $default = null, ?int $branchId = null)
    {
        $targetBranchId = self::resolveBranchId($branchId);
        $cacheKey = "sys_setting_{$key}_branch_{$targetBranchId}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $targetBranchId) {
            $setting = self::where('key', $key)->where('branch_id', $targetBranchId)->first();
            
            if (!$setting && $targetBranchId !== 1) {
                $setting = self::where('key', $key)->where('branch_id', 1)->first();
            }

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
     * Set a setting value
     */
    public static function set($key, $value, ?int $branchId = null)
    {
        $targetBranchId = self::resolveBranchId($branchId);

        $setting = self::updateOrCreate(
            ['key' => $key, 'branch_id' => $targetBranchId],
            ['value' => $value]
        );

        Cache::forget("setting_{$key}");
        Cache::forget("sys_setting_{$key}_branch_{$targetBranchId}");
        Cache::forget("sys_setting_{$key}_branch_1");
        
        return $setting;
    }

    /**
     * Cast value to appropriate type
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all settings grouped by group
     */
    public static function getAllGrouped(?int $branchId = null)
    {
        $targetBranchId = self::resolveBranchId($branchId);
        return self::where('branch_id', $targetBranchId)->get()->groupBy('group');
    }
}
