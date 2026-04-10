<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi Baru Diterima</title>
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
        .donation-icon {
            width: 60px;
            height: 60px;
            background-color: #007bff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .donation-icon::before {
            content: "💰";
            font-size: 30px;
        }
        h1 {
            color: #007bff;
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
        .donor-info {
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
        .status-success {
            color: #28a745;
            font-weight: bold;
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
            <div class="donation-icon"></div>
            <h1>Donasi Baru Diterima!</h1>
            <p>Ada donasi baru untuk campaign Anda</p>
        </div>

        <div class="greeting">
            <p>Halo <strong>{{ $campaignOwner->name }}</strong>!</p>
            <p>Selamat! Ada donasi baru yang diterima untuk campaign Anda.</p>
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
                <span class="detail-label">✅ Status:</span>
                <span class="detail-value status-success">Berhasil</span>
            </div>
        </div>

        <div class="donor-info">
            <div class="section-title">Informasi Donatur</div>
            @if($donation->is_anonymous)
                <p><strong>👤 Nama:</strong> Anonim</p>
            @else
                <p><strong>👤 Nama:</strong> {{ $donor->name }}</p>
                <p><strong>📧 Email:</strong> {{ $donor->email }}</p>
            @endif
            
            @if($donation->message)
            <p><strong>💬 Pesan:</strong></p>
            <p style="font-style: italic; margin-left: 20px;">"{{ $donation->message }}"</p>
            @endif
        </div>

        <div class="campaign-info">
            <div class="section-title">Campaign Anda</div>
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
                Kelola Campaign
            </a>
        </div>

        <div class="footer">
            <p><strong>Beramal</strong> - Platform Donasi Online Terpercaya</p>
            <p>Terima kasih telah menggunakan platform Beramal!</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi tim support kami.</p>
        </div>
    </div>
</body>
</html>
