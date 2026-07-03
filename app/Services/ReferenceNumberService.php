<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferenceNumberService
{
    private const PREFIX = 'ZOI/LTR';
    private const SEQUENCE_LENGTH = 6;

    public function generate(): string
    {
        $year = date('Y');
        $sequence = $this->getNextSequence($year);
        
        return sprintf(
            '%s/%d/%s',
            self::PREFIX,
            $year,
            str_pad($sequence, self::SEQUENCE_LENGTH, '0', STR_PAD_LEFT)
        );
    }

    private function getNextSequence(int $year): int
    {
        try {
            return DB::transaction(function () use ($year) {
                $lastDoc = Document::whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();

                if ($lastDoc && preg_match('/ZOI\/LTR\/' . $year . '\/(\d+)/', $lastDoc->reference_no, $matches)) {
                    return (int) $matches[1] + 1;
                }

                return 1;
            });
        } catch (\Exception $e) {
            Log::error('Reference number generation failed', [
                'error' => $e->getMessage(),
                'year' => $year,
            ]);
            return rand(100000, 999999);
        }
    }
}
