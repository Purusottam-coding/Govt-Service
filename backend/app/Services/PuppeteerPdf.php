<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;

class PuppeteerPdf
{
    protected string $pdfContent;

    public function __construct(string $pdfContent)
    {
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get raw binary string of the generated PDF.
     */
    public function output(): string
    {
        return $this->pdfContent;
    }

    /**
     * Return a download HTTP response with proper PDF headers.
     */
    public function download(string $filename = 'document.pdf'): Response
    {
        return response($this->pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($this->pdfContent),
            'Cache-Control' => 'private, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Return an inline stream HTTP response for viewing in browser.
     */
    public function stream(string $filename = 'document.pdf'): Response
    {
        return response($this->pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Content-Length' => strlen($this->pdfContent),
        ]);
    }

    /**
     * Save the PDF directly to a local filesystem path.
     */
    public function save(string $path): self
    {
        file_put_contents($path, $this->pdfContent);
        return $this;
    }
}
