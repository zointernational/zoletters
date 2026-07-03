<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'sort_order',
    ];

    public const GROUP_GENERAL = 'general';
    public const GROUP_COMPANY = 'company';
    public const GROUP_DOCUMENT = 'document';

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function allAsKeyValue(): array
    {
        return static::all()
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function defaultSettings(): array
    {
        return [
            ['key' => 'company_name', 'value' => 'ZO International', 'type' => 'text', 'group' => self::GROUP_COMPANY, 'label' => 'Company Name', 'sort_order' => 1],
            ['key' => 'company_address', 'value' => '', 'type' => 'textarea', 'group' => self::GROUP_COMPANY, 'label' => 'Company Address', 'sort_order' => 2],
            ['key' => 'company_phone', 'value' => '', 'type' => 'text', 'group' => self::GROUP_COMPANY, 'label' => 'Phone', 'sort_order' => 3],
            ['key' => 'company_email', 'value' => '', 'type' => 'email', 'group' => self::GROUP_COMPANY, 'label' => 'Email', 'sort_order' => 4],
            ['key' => 'company_website', 'value' => '', 'type' => 'url', 'group' => self::GROUP_COMPANY, 'label' => 'Website', 'sort_order' => 5],
            ['key' => 'reference_prefix', 'value' => 'ZOI/LTR', 'type' => 'text', 'group' => self::GROUP_DOCUMENT, 'label' => 'Reference Prefix', 'sort_order' => 10],
            ['key' => 'use_financial_year', 'value' => '0', 'type' => 'boolean', 'group' => self::GROUP_DOCUMENT, 'label' => 'Use Financial Year', 'sort_order' => 11],
            ['key' => 'financial_year_start_month', 'value' => '4', 'type' => 'number', 'group' => self::GROUP_DOCUMENT, 'label' => 'Financial Year Start Month', 'sort_order' => 12],
            ['key' => 'default_page_size', 'value' => 'A4', 'type' => 'select', 'group' => self::GROUP_DOCUMENT, 'label' => 'Default Page Size', 'sort_order' => 13, 'options' => json_encode(['A4', 'A5', 'Letter', 'Legal'])],
            ['key' => 'default_orientation', 'value' => 'portrait', 'type' => 'select', 'group' => self::GROUP_DOCUMENT, 'label' => 'Default Orientation', 'sort_order' => 14, 'options' => json_encode(['portrait', 'landscape'])],
        ];
    }

    public static function bootDefaultSettings(): void
    {
        if (static::count() === 0) {
            foreach (static::defaultSettings() as $setting) {
                static::create($setting);
            }
        }
    }
}
