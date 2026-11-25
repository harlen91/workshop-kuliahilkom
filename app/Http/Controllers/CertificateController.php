<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\CertificateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService,
    ) {}

    /**
     * Download the participant's e-certificate as a PDF file.
     */
    public function download(Registration $registration): Response
    {
        Gate::authorize('downloadCertificate', $registration);

        $registration->loadMissing(['user', 'workshop.speaker', 'workshop.category']);

        abort_unless($registration->hasCertificate(), 404, 'Sertifikat belum tersedia untuk pendaftaran ini.');

        return $this->certificateService->download($registration);
    }

    /**
     * Stream the participant's e-certificate PDF inline in the browser.
     */
    public function preview(Registration $registration): Response
    {
        Gate::authorize('downloadCertificate', $registration);

        $registration->loadMissing(['user', 'workshop.speaker', 'workshop.category']);

        abort_unless($registration->hasCertificate(), 404, 'Sertifikat belum tersedia untuk pendaftaran ini.');

        return $this->certificateService->stream($registration);
    }

    /**
     * Publicly verify a certificate by its unique certificate code.
     */
    public function verify(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));
        $registration = null;
        $isValid = false;

        if ($code !== '') {
            $candidate = Registration::with(['user', 'workshop.speaker', 'workshop.category'])
                ->where('certificate_code', $code)
                ->first();

            if ($candidate && $candidate->hasCertificate()) {
                $registration = $candidate;
                $isValid = true;
            }
        }

        return view('certificates.verify', [
            'code' => $code,
            'registration' => $registration,
            'isValid' => $isValid,
        ]);
    }
}
