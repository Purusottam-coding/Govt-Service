<?php

namespace App\Services;

use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

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
     * Compile all certificate payload data for an application.
     */
    public function getCertificateData(Application $application): array
    {
        $application->loadMissing(['user', 'service.department', 'payment']);

        $certNumber = $application->certificate_number ?: $application->application_number;
        $verificationUrl = url('/verify?query=' . urlencode($certNumber));

        $qrCodeDataUri = $this->generateQrCode($verificationUrl);
        $emblemBase64 = $this->getImageBase64('images/Emblem_of_Nepal.png');
        $flagBase64 = $this->getImageBase64('images/nepal-flag.png') ?: $this->getImageBase64('images/nepal-flag.gif');

        $issuedAt = $application->issued_at ?: ($application->processed_at ?: now());
        $dispatchNumber = 'CH-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT);
        $fiscalYear = '२०८३/०८४';

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
            'departmentName' => $application->service->department->name ?? 'प्रशासन शाखा',
            'serviceName' => $application->service->name ?? 'सरकारी सेवा',
            'applicantName' => $application->applicant_name,
            'applicantAddress' => $application->applicant_address ?: 'बाह्रदशी गाउँपालिका, झापा',
            'applicantPhone' => $application->applicant_phone,
            'adminRemarks' => $application->admin_remarks ?: 'नियमानुसार सम्पूर्ण प्रक्रिया र प्रमाण रुजु गरी यो आधिकारिक प्रमाणपत्र जारी गरिएको छ।',
        ];
    }

    /**
     * Generate Dompdf instance for the certificate.
     */
    public function generatePdf(Application $application): DomPdfWrapper
    {
        $data = $this->getCertificateData($application);
        $data['isPdf'] = true;

        $pdf = Pdf::loadView('certificates.official', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        return $pdf;
    }
}
