<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi Berhasil - Terima Kasih!</title>
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
        .success-icon {
            width: 60px;
            height: 60px;
            background-color: #28a745;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .success-icon::before {
            content: "✓";
            color: white;
            font-size: 30px;
            font-weight: bold;
        }
        h1 {
            color: #28a745;
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
            color: #28a745;
        }
        .message {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .campaign-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #007bff;
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
            <div class="success-icon"></div>
            <h1>Donasi Berhasil!</h1>
            <p>Terima kasih atas kebaikan hati Anda</p>
        </div>

        <div class="greeting">
            <p>Halo <strong>{{ $donor->name }}</strong>!</p>
            <p>Terima kasih atas kebaikan hati Anda! Donasi Anda telah berhasil diproses.</p>
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
                <span class="detail-value">{{ $donation->paid_at->format('d F Y, H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">💳 Metode Pembayaran:</span>
                <span class="detail-value">{{ ucfirst($donation->payment_method) }}</span>
            </div>
        </div>

        @if($donation->message)
        <div class="message">
            <strong>💬 Pesan Anda:</strong><br>
            "{{ $donation->message }}"
        </div>
        @endif

        <div class="campaign-info">
            <div class="section-title">Campaign yang Didukung</div>
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
            <div class="detail-row">
                <span class="detail-label">🎯 Target:</span>
                <span class="detail-value">Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('campaign.show', $campaign->slug) }}" class="cta-button">
                Lihat Campaign
            </a>
        </div>

        <div class="footer">
            <p><strong>Beramal</strong> - Platform Donasi Online Terpercaya</p>
            <p>Semoga kebaikan Anda mendapat balasan yang berlimpah!</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi tim support kami.</p>
        </div>
    </div>
</body>
</html>
