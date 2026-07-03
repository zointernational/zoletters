<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use TCPDF;

class PdfService
{
    private const APP_NAME = 'ZO Letters';
    private const APP_VERSION = '1.1.0';
    private const STORAGE_PATH = 'documents';

    public function generate(Document $document, bool $force = false): ?string
    {
        if (!$force && $document->pdf_file && $this->pdfExists($document)) {
            Log::channel('daily')->info('PDF reuse', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);
            return $document->pdf_file;
        }

        if (!$document->template) {
            Log::channel('daily')->error('PDF generation failed: no template', [
                'document_id' => $document->id,
            ]);
            return null;
        }

        try {
            $filename = $this->generateFilename($document);
            $filepath = $this->getStoragePath($filename);

            $pdf = $this->createPdf($document);
            $pdf->Output($filepath, 'F');

            if (!file_exists($filepath)) {
                throw new \Exception('PDF file was not created');
            }

            Log::channel('daily')->info('PDF generated', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
                'filename' => $filename,
            ]);

            return $filename;
        } catch (\Throwable $e) {
            Log::channel('daily')->error('PDF generation failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    public function preview(Document $document): ?string
    {
        if (!$document->template) {
            return null;
        }

        try {
            $pdf = $this->createPdf($document);
            return $pdf->Output('preview.pdf', 'S');
        } catch (\Throwable $e) {
            Log::channel('daily')->error('PDF preview failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function createPdf(Document $document): TCPDF
    {
        $template = $document->template;

        $pageFormat = $this->getPageFormat($template->page_size);
        $orientation = strtoupper(substr($template->orientation, 0, 1));

        $pdf = new TCPDF($orientation, 'mm', $pageFormat, true, 'UTF-8', true);

        $pdf->SetCreator($this->getCreatorString());
        $pdf->SetAuthor($this->APP_NAME);
        $pdf->SetTitle($document->subject ?: 'Document');
        $pdf->SetSubject($document->template->name ?: 'Letter');
        $pdf->SetKeywords('ZO Letters, Document, ' . $document->reference_no);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(
            (float) $template->margin_left,
            (float) $template->margin_top,
            (float) $template->margin_right
        );

        $pdf->SetAutoPageBreak(true, (float) $template->margin_bottom + 10);

        $pdf->AddPage();

        $this->addHeader($pdf, $template);
        $this->addFooter($pdf, $template);
        $this->addContent($pdf, $document);

        return $pdf;
    }

    private function addHeader(TCPDF $pdf, Template $template): void
    {
        if (!$template->hasHeaderImage()) {
            return;
        }

        $imagePath = public_path('uploads/templates/' . $template->header_image);
        if (!file_exists($imagePath)) {
            Log::channel('daily')->warning('Header image not found', [
                'image' => $template->header_image,
            ]);
            return;
        }

        $pdf->Image(
            $imagePath,
            $template->margin_left,
            10,
            $this->getUsableWidth($template),
            0,
            '',
            '',
            'T',
            false,
            300,
            'T',
            false,
            false,
            0,
            false,
            false,
            true
        );

        $imageInfo = getimagesize($imagePath);
        if ($imageInfo) {
            $headerHeight = ($imageInfo[1] / $imageInfo[0]) * $this->getUsableWidth($template);
            $pdf->SetY(10 + $headerHeight + 5);
        }
    }

    private function addFooter(TCPDF $pdf, Template $template): void
    {
        if (!$template->hasFooterImage()) {
            $pdf->SetY(-15);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->Cell(0, 10, 'Page ' . $pdf->getAliasNumPage() . ' of ' . $pdf->getAliasNbPages(), 0, false, 'C');
            return;
        }

        $imagePath = public_path('uploads/templates/' . $template->footer_image);
        if (!file_exists($imagePath)) {
            Log::channel('daily')->warning('Footer image not found', [
                'image' => $template->footer_image,
            ]);
            $pdf->SetY(-15);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->Cell(0, 10, 'Page ' . $pdf->getAliasNumPage() . ' of ' . $pdf->getAliasNbPages(), 0, false, 'C');
            return;
        }

        $pdf->Image(
            $imagePath,
            $template->margin_left,
            -20,
            $this->getUsableWidth($template),
            0,
            '',
            '',
            'B',
            false,
            300,
            'B',
            false,
            false,
            0,
            false,
            false,
            true
        );
    }

    private function addContent(TCPDF $pdf, Document $document): void
    {
        $html = $this->prepareHtml($document);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetY($pdf->GetY());

        $pdf->writeHTML($html, true, false, true, false, 'J');
    }

    private function prepareHtml(Document $document): string
    {
        $html = '<div style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.6;">';

        $html .= '<p><strong>Ref: ' . htmlspecialchars($document->reference_no) . '</strong></p>';
        $html .= '<p><strong>Date: ' . $document->created_at->format('F d, Y') . '</strong></p>';
        $html .= '<br/>';

        $html .= '<p><strong>To:</strong><br/>';
        $html .= htmlspecialchars($document->recipient_name) . '<br/>';
        $html .= nl2br(htmlspecialchars($document->recipient_address)) . '</p>';
        $html .= '<br/>';

        $html .= '<p><strong>Subject: ' . htmlspecialchars($document->subject) . '</strong></p>';
        $html .= '<br/><br/>';

        $html .= $document->body_html;

        $html .= '</div>';
        return $html;
    }

    private function getPageFormat(string $size): string
    {
        return match ($size) {
            'A5' => 'A5',
            'Letter' => 'LETTER',
            'Legal' => 'LEGAL',
            default => 'A4',
        };
    }

    private function getUsableWidth(Template $template): float
    {
        $pageWidth = match ($template->page_size) {
            'A5' => 148,
            'Letter' => 216,
            'Legal' => 216,
            default => 210,
        };

        if ($template->orientation === 'landscape') {
            $pageWidth += 80;
        }

        return $pageWidth - (float) $template->margin_left - (float) $template->margin_right;
    }

    private function getStoragePath(string $filename): string
    {
        $path = storage_path('app/' . self::STORAGE_PATH);
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path . '/' . $filename;
    }

    private function generateFilename(Document $document): string
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document->reference_no);
        return $sanitized . '_' . $document->id . '_' . time() . '.pdf';
    }

    private function pdfExists(Document $document): bool
    {
        if (!$document->pdf_file) {
            return false;
        }
        $path = storage_path('app/' . self::STORAGE_PATH . '/' . $document->pdf_file);
        return file_exists($path);
    }

    private function getCreatorString(): string
    {
        return self::APP_NAME . ' v' . self::APP_VERSION;
    }

    public function delete(Document $document): bool
    {
        if (!$document->pdf_file) {
            return true;
        }

        try {
            $path = storage_path('app/' . self::STORAGE_PATH . '/' . $document->pdf_file);
            if (file_exists($path)) {
                unlink($path);
            }
            Log::channel('daily')->info('PDF deleted', [
                'document_id' => $document->id,
                'filename' => $document->pdf_file,
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::channel('daily')->error('PDF deletion failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getFilePath(Document $document): ?string
    {
        if (!$document->pdf_file) {
            return null;
        }

        $path = storage_path('app/' . self::STORAGE_PATH . '/' . $document->pdf_file);
        return file_exists($path) ? $path : null;
    }

    public function getVersion(): string
    {
        return self::APP_VERSION;
    }
}
