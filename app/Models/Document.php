<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'reference_no',
        'recipient_name',
        'recipient_address',
        'subject',
        'body_html',
        'pdf_file',
        'created_by',
    ];

    protected $casts = [
        'template_id' => 'integer',
        'created_by' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function getCleanBodyAttribute(): string
    {
        return strip_tags($this->body_html);
    }
}
