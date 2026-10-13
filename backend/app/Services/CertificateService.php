<?php

namespace App\Services;

use App\Models\Application;
use App\Support\NepaliUnicode;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class CertificateService
{
    /**
     * Generate an offline QR Code as a Data-URI string (SVG or PNG).
     */
    public function generateQrCode(string $content): string
    {
        try {
            return (new QRCode())->render($content);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Get base64 encoded string of a public image.
     */
    public function getImageBase64(string $relativePath): ?string
    {
        $path = public_path($relativePath);
        if (file_exists($path)) {
            $mime = mime_content_type($path) ?: 'image/png';
            $data = file_get_contents($path);
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        }
        return null;
    }

    /**
     * Compile all certificate payload data for an application with clean Unicode UTF-8 normalization.
     */
    public function getCertificateData(Application $application): array
    {
        $application->loadMissing(['user', 'service.department', 'payment']);

        $certNumber = $application->certificate_number ?: $application->application_number;
        $verificationUrl = route('public.verify', ['code' => $certNumber]);

        $qrCodeDataUri = $this->generateQrCode($verificationUrl);
        $emblemBase64 = $this->getImageBase64('images/Emblem_of_Nepal.png');
        $flagBase64 = $this->getImageBase64('images/nepal-flag.png') ?: $this->getImageBase64('images/nepal-flag.gif');

        $issuedAt = $application->issued_at ?: ($application->processed_at ?: now());
        $dispatchNumber = 'CH-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT);
        $fiscalYear = '२०८३/०८४';

        // Clean and normalize Unicode UTF-8 strings (preserving English names, addresses, and remarks)
        $rawApplicantName = (string) $application->applicant_name;
        $applicantName = NepaliUnicode::normalize($rawApplicantName);

        $rawAddress = (string) ($application->applicant_address ?: 'बाह्रदशी गाउँपालिका, झापा');
        $applicantAddress = NepaliUnicode::normalize($rawAddress);

        $serviceName = NepaliUnicode::normalize($application->service->name ?? 'सरकारी सेवा');
        $departmentName = NepaliUnicode::normalize($application->service->department->name ?? 'प्रशासन शाखा');
        $rawRemarks = (string) ($application->admin_remarks ?: 'नियमानुसार सम्पूर्ण प्रक्रिया र प्रमाण रुजु गरी यो आधिकारिक प्रमाणपत्र जारी गरिएको छ।');
        $adminRemarks = NepaliUnicode::normalize($rawRemarks);

        // Load Noto Sans Devanagari fonts (embedded directly as base64 for reliable, zero-latency rendering)
        $fontRegPath = public_path('fonts/NotoSansDevanagari-Regular.ttf');
        $fontBoldPath = public_path('fonts/NotoSansDevanagari-Bold.ttf');

        $fontRegular = file_exists($fontRegPath)
            ? 'data:font/truetype;charset=utf-8;base64,' . base64_encode(file_get_contents($fontRegPath))
            : str_replace('\\', '/', $fontRegPath);

        $fontBold = file_exists($fontBoldPath)
            ? 'data:font/truetype;charset=utf-8;base64,' . base64_encode(file_get_contents($fontBoldPath))
            : str_replace('\\', '/', $fontBoldPath);

        return [
            'application' => $application,
            'certificateNumber' => $certNumber,
            'verificationUrl' => $verificationUrl,
            'qrCodeDataUri' => $qrCodeDataUri,
            'emblemBase64' => $emblemBase64,
            'flagBase64' => $flagBase64,
            'issuedAt' => $issuedAt,
            'dispatchNumber' => $dispatchNumber,
            'fiscalYear' => $fiscalYear,
            'departmentName' => $departmentName,
            'serviceName' => $serviceName,
            'applicantName' => $applicantName,
            'applicantAddress' => $applicantAddress,
            'applicantPhone' => $application->applicant_phone,
            'fontRegular' => $fontRegular,
            'fontBold' => $fontBold,
            'adminRemarks' => $adminRemarks,
        ];
    }

    /**
     * Generate high-fidelity PDF instance using Puppeteer (Chromium engine with OpenType HarfBuzz text shaping).
     * Falls back to DomPDF if Puppeteer is unavailable.
     */
    public function generatePdf(Application $application): PuppeteerPdf|DomPdfWrapper
    {
        $data = $this->getCertificateData($application);
        $data['isPdf'] = true;

        // 1. Primary Engine: Puppeteer with Headless Chromium & Noto Sans Devanagari embedded
        try {
            $html = view('certificates.official', $data)->render();
            $tempHtml = tempnam(sys_get_temp_dir(), 'cert_html_') . '.html';
            $tempPdf = tempnam(sys_get_temp_dir(), 'cert_pdf_') . '.pdf';
            file_put_contents($tempHtml, $html);

            $scriptPath = base_path('scripts/generate-pdf.cjs');
            $process = new Process([
                'node',
                $scriptPath,
                '--html-file',
                $tempHtml,
                '--output',
                $tempPdf,
            ], base_path());

            $process->setTimeout(60);
            $process->run();

            if ($process->isSuccessful() && file_exists($tempPdf) && filesize($tempPdf) > 0) {
                $pdfBinary = file_get_contents($tempPdf);
                @unlink($tempHtml);
                @unlink($tempPdf);
                return new PuppeteerPdf($pdfBinary);
            }

            Log::warning('Puppeteer execution did not produce PDF, falling back to DomPDF: ' . $process->getErrorOutput());
            @unlink($tempHtml);
            @unlink($tempPdf);
        } catch (\Throwable $e) {
            Log::warning('Puppeteer exception, falling back to DomPDF: ' . $e->getMessage());
        }

        // 2. Safety Fallback: DomPDF
        $pdf = Pdf::loadView('certificates.official', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Noto Sans Devanagari',
            'enable_font_subsetting' => true,
        ]);

        return $pdf;
    }
}
