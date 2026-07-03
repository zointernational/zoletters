<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'header_image',
        'footer_image',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'page_size',
        'orientation',
        'status',
    ];

    protected $casts = [
        'margin_top' => 'decimal:2',
        'margin_bottom' => 'decimal:2',
        'margin_left' => 'decimal:2',
        'margin_right' => 'decimal:2',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function hasHeaderImage(): bool
    {
        return !empty($this->header_image);
    }

    public function hasFooterImage(): bool
    {
        return !empty($this->footer_image);
    }
}
