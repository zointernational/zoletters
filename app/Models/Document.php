<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_FINAL = 'final';
    public const STATUS_PRINTED = 'printed';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_FINAL,
        self::STATUS_PRINTED,
    ];

    protected $fillable = [
        'template_id',
        'reference_no',
        'recipient_name',
        'recipient_address',
        'subject',
        'body_html',
        'pdf_file',
        'status',
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

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeFinal($query)
    {
        return $query->where('status', self::STATUS_FINAL);
    }

    public function scopePrinted($query)
    {
        return $query->where('status', self::STATUS_PRINTED);
    }

    public function scopeByDateRange($query, $from = null, $to = null)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    public function getCleanBodyAttribute(): string
    {
        return strip_tags($this->body_html);
    }

    public function getWordCountAttribute(): int
    {
        return str_word_count($this->clean_body);
    }

    public function getCharacterCountAttribute(): int
    {
        return strlen(strip_tags($this->body_html));
    }

    public function markAsFinal(): void
    {
        $this->update(['status' => self::STATUS_FINAL]);
    }

    public function markAsPrinted(): void
    {
        $this->update(['status' => self::STATUS_PRINTED]);
    }

    public function markAsDraft(): void
    {
        $this->update(['status' => self::STATUS_DRAFT]);
    }
}
