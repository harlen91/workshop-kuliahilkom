<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\Workshop;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    /**
     * Build the public verification URL embedded in the certificate QR code.
     */
    public function getVerificationUrl(Registration $registration): string
    {
        return route('certificates.verify', ['code' => $registration->certificate_code]);
    }

    /**
     * Generate an SVG QR code data URI pointing to the certificate verification endpoint.
     */
    public function generateVerificationQrDataUri(Registration $registration): ?string
    {
        if (empty($registration->certificate_code)) {
            return null;
        }

        try {
            $svg = (string) QrCode::format('svg')
                ->size(110)
                ->margin(1)
                ->color(15, 23, 42)
                ->generate($this->getVerificationUrl($registration));

            return 'data:image/svg+xml;base64,'.base64_encode($svg);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Encode an image stored on the public disk as a base64 data URI for DomPDF.
     */
    public function encodePublicImageToDataUri(?string $path, bool $stripWhiteBackground = false): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_contains($path, '..')) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $contents = $disk->get($path);

        if ($contents === null || $contents === '') {
            return null;
        }

        if ($stripWhiteBackground) {
            $transparentUri = $this->stripWhiteBackgroundToTransparentPng($contents);

            if ($transparentUri !== null) {
                return $transparentUri;
            }
        }

        $mime = $disk->mimeType($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }

    /**
     * Build the view data array required to render the certificate PDF template.
     *
     * @return array{
     *     participantName: string,
     *     participantInstitution: ?string,
     *     workshopTitle: string,
     *     eventTypeLabel: string,
     *     categoryName: ?string,
     *     eventDateFormatted: string,
     *     issuedDateFormatted: string,
     *     certificateCode: string,
     *     signerName: string,
     *     signerTitle: string,
     *     speakerName: ?string,
     *     speakerRole: ?string,
     *     verificationUrl: string,
     *     qrCodeDataUri: ?string,
     *     signatureDataUri: ?string,
     *     stampDataUri: ?string,
     *     backgroundDataUri: ?string,
     * }
     */
    public function buildCertificateData(Registration $registration): array
    {
        $registration->loadMissing(['user', 'workshop.speaker', 'workshop.category']);

        /** @var Workshop $workshop */
        $workshop = $registration->workshop;

        $eventTypeLabel = match ($workshop->type) {
            'webinar' => 'WEBINAR',
            'workshop' => 'WORKSHOP',
            'bootcamp' => 'BOOTCAMP',
            default => 'EVENT',
        };

        $issuedAt = ($registration->attended_at ?? $registration->updated_at ?? now())->copy();

        return [
            'participantName' => $registration->user?->name ?? 'Peserta',
            'participantInstitution' => $registration->user?->institution ?: null,
            'workshopTitle' => $workshop->title,
            'eventTypeLabel' => $eventTypeLabel,
            'categoryName' => $workshop->category?->name,
            'eventDateFormatted' => $this->formatEventDate($workshop),
            'issuedDateFormatted' => $issuedAt->locale('id')->translatedFormat('d F Y'),
            'certificateCode' => (string) $registration->certificate_code,
            'signerName' => filled($workshop->certificate_signer_name)
                ? (string) $workshop->certificate_signer_name
                : ($workshop->speaker?->name ?? 'Tim KuliahIlkom'),
            'signerTitle' => filled($workshop->certificate_signer_title)
                ? (string) $workshop->certificate_signer_title
                : ($workshop->speaker?->title_role ?? 'Instruktur & Penyelenggara KuliahIlkom'),
            'speakerName' => $workshop->speaker?->name,
            'speakerRole' => $workshop->speaker?->title_role,
            'verificationUrl' => $this->getVerificationUrl($registration),
            'qrCodeDataUri' => $this->generateVerificationQrDataUri($registration),
            'signatureDataUri' => $this->encodePublicImageToDataUri($workshop->certificate_signature_path, stripWhiteBackground: true),
            'stampDataUri' => $this->encodePublicImageToDataUri($workshop->certificate_stamp_path, stripWhiteBackground: true),
            'backgroundDataUri' => $this->encodePublicImageToDataUri($workshop->certificate_background_path),
        ];
    }

    /**
     * Render a landscape A4 PDF instance for the given registration.
     */
    public function makePdf(Registration $registration): DomPdfWrapper
    {
        return Pdf::loadView('certificates.pdf', $this->buildCertificateData($registration))
            ->setPaper('a4', 'landscape')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);
    }

    /**
     * Build a clean, deterministic PDF filename for download or streaming.
     */
    public function filename(Registration $registration): string
    {
        $workshopSlug = Str::slug($registration->workshop?->title ?? 'workshop');
        $participantSlug = Str::slug($registration->user?->name ?? 'peserta');

        return "sertifikat-{$workshopSlug}-{$participantSlug}.pdf";
    }

    /**
     * Return an HTTP download response for the generated certificate PDF.
     */
    public function download(Registration $registration): Response
    {
        return $this->makePdf($registration)->download($this->filename($registration));
    }

    /**
     * Return an inline HTTP stream response for previewing the certificate PDF.
     */
    public function stream(Registration $registration): Response
    {
        return $this->makePdf($registration)->stream($this->filename($registration));
    }

    private function formatEventDate(Workshop $workshop): string
    {
        if (! $workshop->start_date) {
            return '-';
        }

        $start = $workshop->start_date->copy()->locale('id');
        $end = $workshop->end_date?->copy()->locale('id');

        if (! $end || $start->isSameDay($end)) {
            return $start->translatedFormat('d F Y');
        }

        if ($start->isSameMonth($end) && $start->isSameYear($end)) {
            return $start->format('d').' – '.$end->translatedFormat('d F Y');
        }

        return $start->translatedFormat('d F Y').' – '.$end->translatedFormat('d F Y');
    }

    /**
     * Convert an opaque white-background signature or stamp image into a transparent PNG data URI.
     */
    private function stripWhiteBackgroundToTransparentPng(string $binary): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring($binary);

        if (! $source instanceof \GdImage) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 1 || $height <= 1) {
            return null;
        }

        $maxDim = max($width, $height);

        if ($maxDim > 600) {
            $newWidth = max(1, (int) round($width * 600 / $maxDim));
            $newHeight = max(1, (int) round($height * 600 / $maxDim));
            $scaled = imagescale($source, $newWidth, $newHeight);

            if ($scaled instanceof \GdImage) {
                $source = $scaled;
                $width = imagesx($source);
                $height = imagesy($source);
            }
        }

        $corners = [
            [0, 0],
            [$width - 1, 0],
            [0, $height - 1],
            [$width - 1, $height - 1],
        ];

        $whiteOpaqueCorners = 0;

        foreach ($corners as [$cx, $cy]) {
            $rgba = imagecolorsforindex($source, imagecolorat($source, $cx, $cy));

            if ($rgba['alpha'] < 15 && $rgba['red'] >= 235 && $rgba['green'] >= 235 && $rgba['blue'] >= 235) {
                $whiteOpaqueCorners++;
            }
        }

        if ($whiteOpaqueCorners < 3) {
            return null;
        }

        $dest = imagecreatetruecolor($width, $height);

        if (! $dest instanceof \GdImage) {
            return null;
        }

        imagealphablending($dest, false);
        imagesavealpha($dest, true);

        $fullyTransparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorsforindex($source, imagecolorat($source, $x, $y));
                $r = $rgba['red'];
                $g = $rgba['green'];
                $b = $rgba['blue'];
                $origAlpha = $rgba['alpha'];
                $minChannel = min($r, $g, $b);

                if ($minChannel >= 242) {
                    imagesetpixel($dest, $x, $y, $fullyTransparent);

                    continue;
                }

                if ($minChannel >= 210) {
                    $softAlpha = (int) round((($minChannel - 210) / 32) * 127);
                    $alpha = max($origAlpha, min(127, $softAlpha));
                } else {
                    $alpha = $origAlpha;
                }

                $color = imagecolorallocatealpha($dest, $r, $g, $b, $alpha);
                imagesetpixel($dest, $x, $y, $color);
            }
        }

        ob_start();
        imagepng($dest, null, 6);
        $png = ob_get_clean();

        if (! is_string($png) || $png === '') {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($png);
    }
}
