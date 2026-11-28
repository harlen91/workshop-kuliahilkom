<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $certificateCode }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            color: #0f172a;
            background-color: #ffffff;
        }

        .page {
            width: 297mm;
            height: 209mm;
            position: relative;
            overflow: hidden;
        }

        .bg-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            z-index: -1;
        }

        .frame-outer {
            position: fixed;
            top: 9mm;
            right: 9mm;
            bottom: 9mm;
            left: 9mm;
            border: 3px solid #312e81;
            z-index: -1;
        }

        .frame-inner {
            position: fixed;
            top: 12mm;
            right: 12mm;
            bottom: 12mm;
            left: 12mm;
            border: 1px solid #d97706;
            z-index: -1;
        }

        .corner-accent-tl {
            position: fixed;
            top: 9mm;
            left: 9mm;
            width: 18mm;
            height: 18mm;
            border-top: 6px solid #d97706;
            border-left: 6px solid #d97706;
            z-index: -1;
        }

        .corner-accent-br {
            position: fixed;
            right: 9mm;
            bottom: 9mm;
            width: 18mm;
            height: 18mm;
            border-right: 6px solid #d97706;
            border-bottom: 6px solid #d97706;
            z-index: -1;
        }

        .content-wrapper {
            padding: 18mm 24mm 15mm 24mm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-badge {
            display: inline-block;
            background-color: #312e81;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 6px 14px;
            border-radius: 4px;
        }

        .brand-sub {
            font-size: 8pt;
            color: #475569;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .cert-number-box {
            text-align: right;
            font-size: 8.5pt;
            color: #475569;
        }

        .cert-number-value {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e1b4b;
            margin-top: 2px;
        }

        .event-pill {
            display: inline-block;
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            padding: 3px 10px;
            border-radius: 12px;
            margin-bottom: 4px;
        }

        .body-section {
            text-align: center;
            margin-top: 10mm;
        }

        .title-main {
            font-size: 23pt;
            font-weight: bold;
            color: #1e1b4b;
            letter-spacing: 2.5px;
            margin: 0;
        }

        .title-sub {
            font-size: 8.5pt;
            color: #b45309;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 3px;
        }

        .presented-to {
            font-size: 10.5pt;
            color: #475569;
            margin-top: 8mm;
            margin-bottom: 2mm;
        }

        .recipient-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 28pt;
            font-weight: bold;
            color: #1e1b4b;
            margin: 0;
            line-height: 1.15;
        }

        .name-underline {
            width: 145mm;
            height: 2px;
            background-color: #d97706;
            margin: 3mm auto 2.5mm auto;
        }

        .recipient-institution {
            font-size: 10.5pt;
            color: #334155;
            font-weight: bold;
            margin-bottom: 4mm;
        }

        .participation-copy {
            font-size: 10pt;
            color: #475569;
            margin-top: 4mm;
            margin-bottom: 2mm;
        }

        .workshop-title {
            font-size: 15.5pt;
            font-weight: bold;
            color: #312e81;
            margin: 0 auto;
            width: 220mm;
            line-height: 1.3;
        }

        .event-meta {
            font-size: 9.5pt;
            color: #475569;
            margin-top: 3mm;
        }

        .footer-wrapper {
            position: fixed;
            left: 24mm;
            right: 24mm;
            bottom: 17mm;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-cell-left {
            width: 34%;
            vertical-align: bottom;
            text-align: left;
        }

        .footer-cell-center {
            width: 32%;
            vertical-align: bottom;
            text-align: center;
        }

        .footer-cell-right {
            width: 34%;
            vertical-align: bottom;
            text-align: center;
        }

        .qr-table {
            border-collapse: collapse;
        }

        .qr-box {
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 3px;
            border-radius: 4px;
        }

        .qr-caption {
            padding-left: 8px;
            vertical-align: middle;
        }

        .qr-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .qr-code-text {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 7.5pt;
            color: #1e1b4b;
            margin-top: 2px;
        }

        .seal-badge {
            display: inline-block;
            border: 1.5px solid #059669;
            background-color: #ecfdf5;
            color: #065f46;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 5px 12px;
            border-radius: 14px;
        }

        .issued-date {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 5px;
        }

        .signature-area {
            position: relative;
            width: 64mm;
            height: 68px;
            margin: 0 auto 2px auto;
        }

        .signature-img {
            position: relative;
            z-index: 1;
            max-height: 66px;
            max-width: 185px;
            margin-top: 2px;
        }

        .stamp-img {
            position: absolute;
            left: 11mm;
            top: -10px;
            width: 86px;
            height: 86px;
            opacity: 0.85;
            z-index: 3;
        }

        .signature-line {
            position: relative;
            z-index: 1;
            width: 58mm;
            border-top: 1.5px solid #1e293b;
            margin: 0 auto 4px auto;
        }

        .signer-name {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
        }

        .signer-title {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 1px;
        }
    </style>
</head>
<body>
    <div class="page">
        @if($backgroundDataUri)
            <img src="{{ $backgroundDataUri }}" alt="" class="bg-image">
        @else
            <div class="frame-outer"></div>
            <div class="frame-inner"></div>
            <div class="corner-accent-tl"></div>
            <div class="corner-accent-br"></div>
        @endif

        <div class="content-wrapper">
            <table class="header-table">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <span class="brand-badge">KULIAHILKOM</span>
                        <div class="brand-sub">Workshop &amp; Tech Education Platform</div>
                    </td>
                    <td style="width: 50%; vertical-align: top;" class="cert-number-box">
                        <span class="event-pill">{{ $eventTypeLabel }}@if($categoryName) &bull; {{ strtoupper($categoryName) }}@endif</span>
                        <div>Nomor Sertifikat</div>
                        <div class="cert-number-value">{{ $certificateCode }}</div>
                    </td>
                </tr>
            </table>

            <div class="body-section">
                <h1 class="title-main">SERTIFIKAT KEIKUTSERTAAN</h1>
                <div class="title-sub">Certificate of Completion</div>

                <div class="presented-to">Diberikan dengan bangga kepada:</div>

                <div class="recipient-name">{{ $participantName }}</div>
                <div class="name-underline"></div>

                @if($participantInstitution)
                    <div class="recipient-institution">{{ $participantInstitution }}</div>
                @endif

                <div class="participation-copy">
                    Atas partisipasi aktif dan keberhasilannya menyelesaikan program {{ strtolower($eventTypeLabel) }}:
                </div>

                <div class="workshop-title">{{ $workshopTitle }}</div>

                <div class="event-meta">
                    Diselenggarakan pada <strong>{{ $eventDateFormatted }}</strong>
                    @if($speakerName)
                        &nbsp;&bull;&nbsp; Pemateri: <strong>{{ $speakerName }}</strong>@if($speakerRole) ({{ $speakerRole }})@endif
                    @endif
                </div>
            </div>
        </div>

        <div class="footer-wrapper">
            <table class="footer-table">
                <tr>
                    <td class="footer-cell-left">
                        @if($qrCodeDataUri)
                            <table class="qr-table">
                                <tr>
                                    <td class="qr-box">
                                        <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi" width="68" height="68">
                                    </td>
                                    <td class="qr-caption">
                                        <div class="qr-label">Scan untuk Verifikasi</div>
                                        <div class="qr-code-text">{{ $certificateCode }}</div>
                                    </td>
                                </tr>
                            </table>
                        @else
                            <div class="qr-code-text">{{ $certificateCode }}</div>
                        @endif
                    </td>

                    <td class="footer-cell-center">
                        <span class="seal-badge">TERVERIFIKASI &bull; KULIAHILKOM</span>
                        <div class="issued-date">Diterbitkan: {{ $issuedDateFormatted }}</div>
                    </td>

                    <td class="footer-cell-right">
                        <div class="signature-area">
                            @if($signatureDataUri)
                                <img src="{{ $signatureDataUri }}" alt="Tanda Tangan" class="signature-img">
                            @endif
                            @if(! empty($stampDataUri))
                                <img src="{{ $stampDataUri }}" alt="Stempel Resmi" class="stamp-img">
                            @endif
                        </div>
                        <div class="signature-line"></div>
                        <div class="signer-name">{{ $signerName }}</div>
                        <div class="signer-title">{{ $signerTitle }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
