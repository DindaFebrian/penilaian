{{-- @php
    \Carbon\Carbon::setLocale('id');
    $printedAt = \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y');
    $tglVisit  = $evaluation->tanggal
        ? \Carbon\Carbon::parse($evaluation->tanggal)->translatedFormat('d F Y')
        : '-';
    $overallColor = $colorMap[$overallGrade] ?? '#3B82F6';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Visitasi - {{ $school->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 210mm; margin: 0 auto; padding: 20px; background: white; }
        @media print { body { margin: 0; padding: 15px; } .no-print { display: none; } }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #3b82f6; }
        .header h1 { color: #1f2937; margin-bottom: 5px; font-size: 24px; }
        .header p { color: #6b7280; margin: 5px 0; }
        .school-info { background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .school-info table { width: 100%; border-collapse: collapse; }
        .school-info td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .school-info td:first-child { font-weight: bold; width: 220px; }
        .summary-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: center; }
        .summary-box h2 { margin: 0 0 10px 0; font-size: 20px; }
        .overall-grade { font-size: 36px; font-weight: bold; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .print-btn { background-color: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 20px 0; }
        .print-btn:hover { background-color: #2563eb; }
        .note-box { background-color: #fffbeb; padding: 15px; border: 1px solid #fbbf24; border-radius: 4px; margin: 10px 0; }
        .aspect-section { margin-bottom: 30px; page-break-inside: avoid; }
        .aspect-title { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 5px; margin-bottom: 15px; }
        .score-badge { background-color: #f3f4f6; padding: 15px; border-left: 4px solid #3b82f6; margin-bottom: 10px; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 4px; color: #fff; font-weight: bold; }
        .muted { color: #6b7280; font-size: 12px; }
        a.link { color: #2563eb; text-decoration: none; }
        a.link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Laporan</button>
        <button class="print-btn" onclick="window.close()" style="background-color: #6b7280;">❌ Tutup</button>
        <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
            <strong>📋 Preview Laporan</strong><br>
            Ini adalah contoh tampilan laporan yang dihasilkan dari sistem penilaian visitasi sekolah.
        </div>
    </div>

    <div class="header">
        <h1>LAPORAN HASIL VISITASI SEKOLAH</h1>
        <p>Evaluasi Kinerja dan Kelayakan Sekolah</p>
        <p>Tanggal Cetak: {{ $printedAt }}</p>
    </div>

    <div class="school-info">
        <h2 style="margin-top: 0; color: #1f2937;">Informasi Sekolah</h2>
        <table>
            <tr><td>Nama Sekolah</td><td>: {{ $school->nama ?? '-' }}</td></tr>
            <tr><td>NPSN</td><td>: {{ $school->npsn ?? '-' }}</td></tr>
            <tr><td>Alamat</td><td>: {{ $school->alamat ?? '-' }}</td></tr>
            <tr><td>Kepala Sekolah</td><td>: {{ $school->kepala_sekolah ?? '-' }}</td></tr>
            <tr><td>Pengawas/Asesor</td><td>: {{ optional($evaluation->pengawas)->name ?? '-' }}</td></tr>
            <tr><td>Tanggal Visitasi</td><td>: {{ $tglVisit }}</td></tr>
        </table>
    </div>

    <div class="summary-box">
        <h2>HASIL PENILAIAN KESELURUHAN</h2>
        <div class="overall-grade" style="color:#fff;">{{ $overallGrade }}</div>
        <div>{{ $overallText }}</div>
        @if(!is_null($overallAvg))
            <div class="muted">Skor rata-rata: {{ number_format($overallAvg, 2) }} / 4.00</div>
        @endif
    </div>

    <h2 style="color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 5px;">Ringkasan Penilaian Per Aspek</h2>
    <table>
        <thead>
            <tr>
                <th>Aspek Penilaian</th>
                <th style="width: 80px; text-align: center;">Nilai</th>
                <th style="width: 150px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $d)
                @php $c = $colorMap[$d['grade']] ?? '#3B82F6'; @endphp
                <tr>
                    <td>{{ $d['label'] }}</td>
                    <td style="text-align:center; background-color: {{ $c }}; color: #fff; font-weight:bold;">
                        {{ $d['grade'] }}
                    </td>
                    <td>{{ $d['grade_text'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 style="color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 5px; margin-top: 40px;">Detail Penilaian</h2>

    @foreach ($detail as $d)
        <div class="aspect-section">
            <h3 class="aspect-title">{{ $loop->iteration }}. {{ $d['label'] }}</h3>
            <table>
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th>Sub Aspek</th>
                        <th style="width: 100px; text-align: center;">Nilai</th>
                        <th style="width: 160px;">Bukti (opsional)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($d['rows'] as $row)
                        @php $sc = $colorMap[$row['score'] ?? ''] ?? '#3B82F6'; @endphp
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td style="text-align:center; background-color: {{ $sc }}; color: #fff; font-weight: bold;">
                                {{ $row['score'] ?? '-' }}
                            </td>
                            <td>
                                @if(!empty($row['evidence']))
                                    <a class="link" href="{{ asset($row['evidence']) }}" target="_blank">Lihat Bukti</a>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="score-badge">
                <strong>Skor Aspek: </strong>
                @php $bc = $colorMap[$d['grade']] ?? '#3B82F6'; @endphp
                <span class="pill" style="background-color: {{ $bc }};">
                    {{ $d['grade'] }} - {{ $d['grade_text'] }}
                </span>
                @if(!is_null($d['avg']))
                    <span class="muted"> &nbsp; (Rata-rata: {{ number_format($d['avg'], 2) }})</span>
                @endif
            </div>
        </div>
    @endforeach

    <div style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
        <h3 style="color: #1f2937;">Keterangan Nilai:</h3>
        <table style="width: 500px;">
            <tr><td style="background-color: #10B981; color: white; text-align: center; font-weight: bold;">A</td><td>Sangat Baik (3.5 - 4.0)</td></tr>
            <tr><td style="background-color: #3B82F6; color: white; text-align: center; font-weight: bold;">B</td><td>Baik (2.5 - 3.4)</td></tr>
            <tr><td style="background-color: #F59E0B; color: white; text-align: center; font-weight: bold;">C</td><td>Cukup (1.5 - 2.4)</td></tr>
            <tr><td style="background-color: #EF4444; color: white; text-align: center; font-weight: bold;">D</td><td>Perlu Perbaikan (1.0 - 1.4)</td></tr>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: right;">
        <p>{{ $school->city ?? ($evaluation->city ?? 'Bandung') }}, {{ $tglVisit }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p style="border-top: 1px solid #333; display: inline-block; padding-top: 5px;">
            {{ optional($evaluation->pengawas)->name ?? 'Pengawas Sekolah' }}<br>
            <small>Pengawas Sekolah</small>
        </p>
    </div>

    @if(!empty($evaluation->overall_notes))
    <div style="margin-top: 50px; padding: 20px; background-color: #f0f9ff; border-radius: 8px; border-left: 4px solid #0ea5e9;">
        <h4 style="color: #0c4a6e; margin-top: 0;">💡 Rekomendasi/ Catatan Umum:</h4>
        <div style="color:#075985;">{!! nl2br(e($evaluation->overall_notes)) !!}</div>
    </div>
    @endif
</body>
</html> --}}
@php
    \Carbon\Carbon::setLocale('id');
    $printedAt = \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y');
    $tglVisit  = $evaluation->tanggal
        ? \Carbon\Carbon::parse($evaluation->tanggal)->translatedFormat('d F Y')
        : '-';
    $overallColor = $colorMap[$overallGrade] ?? '#3B82F6';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Visitasi - {{ $school->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 210mm; margin: 0 auto; padding: 20px; background: white; }
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
            .col-bukti { display: none; } /* kolom bukti disembunyikan saat print */
        }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #3b82f6; }
        .header h1 { color: #1f2937; margin-bottom: 5px; font-size: 24px; }
        .header p { color: #6b7280; margin: 5px 0; }
        .school-info { background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .school-info table { width: 100%; border-collapse: collapse; }
        .school-info td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .school-info td:first-child { font-weight: bold; width: 220px; }
        .summary-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: center; }
        .summary-box h2 { margin: 0 0 10px 0; font-size: 20px; }
        .overall-grade { font-size: 36px; font-weight: bold; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .print-btn { background-color: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 20px 0; }
        .print-btn:hover { background-color: #2563eb; }
        .note-box { background-color: #fffbeb; padding: 15px; border: 1px solid #fbbf24; border-radius: 4px; margin: 10px 0; }
        .aspect-section { margin-bottom: 30px; page-break-inside: avoid; }
        .aspect-title { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 5px; margin-bottom: 15px; }
        .score-badge { background-color: #f3f4f6; padding: 15px; border-left: 4px solid #3b82f6; margin-bottom: 10px; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 4px; color: #fff; font-weight: bold; }
        .muted { color: #6b7280; font-size: 12px; }
        a.link { color: #2563eb; text-decoration: none; }
        a.link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Laporan</button>
        <button class="print-btn" onclick="window.close()" style="background-color: #6b7280;">❌ Tutup</button>
        <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
            <strong>📋 Preview Laporan</strong><br>
            Ini adalah contoh tampilan laporan yang dihasilkan dari sistem penilaian visitasi sekolah.
        </div>
    </div>

    <div class="header">
        <h1>LAPORAN HASIL VISITASI SEKOLAH</h1>
        <p>Evaluasi Kinerja dan Kelayakan Sekolah</p>
        <p>Tanggal Cetak: {{ $printedAt }}</p>
    </div>

    <div class="school-info">
        <h2 style="margin-top: 0; color: #1f2937;">Informasi Sekolah</h2>
        <table>
            <tr><td>Nama Sekolah</td><td>: {{ $school->nama ?? '-' }}</td></tr>
            <tr><td>NPSN</td><td>: {{ $school->npsn ?? '-' }}</td></tr>
            <tr><td>Alamat</td><td>: {{ $school->alamat ?? '-' }}</td></tr>
            <tr><td>Kepala Sekolah</td><td>: {{ $school->kepala_sekolah ?? '-' }}</td></tr>
            <tr><td>Pengawas/Asesor</td><td>: {{ optional($evaluation->pengawas)->name ?? '-' }}</td></tr>
            <tr><td>Tanggal Visitasi</td><td>: {{ $tglVisit }}</td></tr>
        </table>
    </div>

    <div class="summary-box">
        <h2>HASIL PENILAIAN KESELURUHAN</h2>
        <div class="overall-grade" style="color:#fff;">{{ $overallGrade }}</div>
        <div>{{ $overallText }}</div>
        @if(!is_null($overallAvg))
            <div class="muted">Skor rata-rata: {{ number_format($overallAvg, 2) }} / 4.00</div>
        @endif
    </div>

    <h2 style="color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 5px;">Ringkasan Penilaian Per Aspek</h2>
    <table>
        <thead>
            <tr>
                <th>Aspek Penilaian</th>
                <th style="width: 80px; text-align: center;">Nilai</th>
                <th style="width: 150px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $d)
                @php $c = $colorMap[$d['grade']] ?? '#3B82F6'; @endphp
                <tr>
                    <td>{{ $d['label'] }}</td>
                    <td style="text-align:center; background-color: {{ $c }}; color: #fff; font-weight:bold;">
                        {{ $d['grade'] }}
                    </td>
                    <td>{{ $d['grade_text'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 style="color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 5px; margin-top: 40px;">Detail Penilaian</h2>

    @foreach ($detail as $d)
        <div class="aspect-section">
            <h3 class="aspect-title">{{ $loop->iteration }}. {{ $d['label'] }}</h3>
            <table>
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th>Sub Aspek</th>
                        <th style="width: 100px; text-align: center;">Nilai</th>
                        <th class="col-bukti" style="width: 160px;">Bukti (opsional)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($d['rows'] as $row)
                        @php $sc = $colorMap[$row['score'] ?? ''] ?? '#3B82F6'; @endphp
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td style="text-align:center; background-color: {{ $sc }}; color: #fff; font-weight: bold;">
                                {{ $row['score'] ?? '-' }}
                            </td>
                            <td class="col-bukti">
                                @if(!empty($row['evidence']))
                                    <a class="link" href="{{ asset($row['evidence']) }}" target="_blank">Lihat Bukti</a>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="score-badge">
                <strong>Skor Aspek: </strong>
                @php $bc = $colorMap[$d['grade']] ?? '#3B82F6'; @endphp
                <span class="pill" style="background-color: {{ $bc }};">
                    {{ $d['grade'] }} - {{ $d['grade_text'] }}
                </span>
                @if(!is_null($d['avg']))
                    <span class="muted"> &nbsp; (Rata-rata: {{ number_format($d['avg'], 2) }})</span>
                @endif
            </div>
        </div>
    @endforeach

    <div style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
        <h3 style="color: #1f2937;">Keterangan Nilai:</h3>
        <table style="width: 500px;">
            <tr><td style="background-color: #10B981; color: white; text-align: center; font-weight: bold;">A</td><td>Sangat Baik (3.5 - 4.0)</td></tr>
            <tr><td style="background-color: #3B82F6; color: white; text-align: center; font-weight: bold;">B</td><td>Baik (2.5 - 3.4)</td></tr>
            <tr><td style="background-color: #F59E0B; color: white; text-align: center; font-weight: bold;">C</td><td>Cukup (1.5 - 2.4)</td></tr>
            <tr><td style="background-color: #EF4444; color: white; text-align: center; font-weight: bold;">D</td><td>Perlu Perbaikan (1.0 - 1.4)</td></tr>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: right;">
        <p>{{ $school->city ?? ($evaluation->city ?? 'Cianjur') }}, {{ $tglVisit }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p style="border-top: 1px solid #333; display: inline-block; padding-top: 5px;">
            {{ optional($evaluation->pengawas)->name ?? 'Pengawas Sekolah' }}<br>
            <small>Pengawas Sekolah</small>
        </p>
    </div>

    @if(!empty($evaluation->overall_notes))
    <div class="col-bukti" style="margin-top: 50px; padding: 20px; background-color: #f0f9ff; border-radius: 8px; border-left: 4px solid #0ea5e9;">
        <h4 style="color: #0c4a6e; margin-top: 0;">💡 Rekomendasi/ Catatan Umum:</h4>
        <div style="color:#075985;">{!! nl2br(e($evaluation->overall_notes)) !!}</div>
    </div>
    @endif
</body>
</html>
