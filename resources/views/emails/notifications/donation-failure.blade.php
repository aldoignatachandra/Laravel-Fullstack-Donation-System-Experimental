<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi Gagal Diproses</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .failure-icon {
            width: 60px;
            height: 60px;
            background-color: #dc3545;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .failure-icon::before {
            content: "⚠️";
            font-size: 30px;
        }
        h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .greeting {
            color: #6c757d;
            margin-bottom: 20px;
        }
        .donation-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            color: #495057;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
        }
        .status-failed {
            color: #dc3545;
            font-weight: bold;
        }
        .campaign-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .troubleshooting {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
        }
        .step-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .step-list li {
            margin-bottom: 8px;
        }
        .support-info {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="failure-icon"></div>
            <h1>Donasi Gagal Diproses</h1>
            <p>Kami ingin menginformasikan tentang kendala pembayaran</p>
        </div>

        <div class="greeting">
            <p>Halo <strong>{{ $donor->name }}</strong>!</p>
            <p>Kami ingin menginformasikan bahwa donasi Anda mengalami kendala dalam proses pembayaran.</p>
        </div>

        <div class="donation-details">
            <div class="section-title">Detail Donasi</div>
            <div class="detail-row">
                <span class="detail-label">📋 Nomor Donasi:</span>
                <span class="detail-value">#{{ $donation->order_id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">💰 Jumlah:</span>
                <span class="detail-value amount">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">📅 Tanggal:</span>
                <span class="detail-value">{{ $donation->created_at->format('d F Y, H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">⚠️ Status:</span>
                <span class="detail-value status-failed">{{ $statusText }}</span>
            </div>
        </div>

        @if($donation->message)
        <div class="support-info">
            <strong>💬 Pesan Anda:</strong><br>
            "{{ $donation->message }}"
        </div>
        @endif

        <div class="campaign-info">
            <div class="section-title">Campaign yang Ingin Didukung</div>
            <h4>🎯 {{ $campaign->title }}</h4>
            <p>{{ Str::limit($campaign->description, 150) }}</p>
            <div class="detail-row">
                <span class="detail-label">📊 Progress:</span>
                <span class="detail-value">{{ number_format(($campaign->total_donations / $campaign->target_amount) * 100, 1) }}%</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">💵 Total Terkumpul:</span>
                <span class="detail-value">Rp {{ number_format($campaign->total_donations, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="troubleshooting">
            <div class="section-title">Langkah Selanjutnya</div>
            <p>Jangan khawatir! Berikut adalah langkah-langkah yang dapat Anda lakukan:</p>
            <ol class="step-list">
                <li><strong>Periksa saldo atau limit kartu</strong> - Pastikan ada dana yang cukup</li>
                <li><strong>Pastikan informasi pembayaran benar</strong> - Cek nomor kartu dan CVV</li>
                <li><strong>Coba metode pembayaran berbeda</strong> - Gunakan kartu atau e-wallet lain</li>
                <li><strong>Hubungi bank jika diperlukan</strong> - Mungkin ada pembatasan transaksi</li>
                <li><strong>Coba lagi dalam beberapa menit</strong> - Kadang ada gangguan sementara</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('campaign.show', $campaign->slug) }}" class="cta-button">
                Coba Donasi Lagi
            </a>
        </div>

        <div class="support-info">
            <p><strong>🆘 Butuh Bantuan?</strong></p>
            <p>Jika masalah berlanjut, silakan hubungi tim support kami:</p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>📧 Email: support@beramal.com</li>
                <li>📞 Telepon: +62-XXX-XXXX-XXXX</li>
                <li>💬 Live Chat: Tersedia di website</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>Beramal</strong> - Platform Donasi Online Terpercaya</p>
            <p>Terima kasih atas kesabaran Anda dan semoga campaign ini tetap berjalan lancar!</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>Kami siap membantu Anda kapan saja.</p>
        </div>
    </div>
</body>
</html>
