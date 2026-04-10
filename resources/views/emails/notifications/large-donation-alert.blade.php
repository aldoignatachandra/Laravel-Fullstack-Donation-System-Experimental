<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALERT: Donasi Besar Diterima</title>
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
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
        }
        .alert-icon {
            width: 60px;
            height: 60px;
            background-color: #dc3545;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .alert-icon::before {
            content: "⚠️";
            font-size: 30px;
        }
        h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .alert-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #721c24;
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
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
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
        .action-steps {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
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
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
        .step-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .step-list li {
            margin-bottom: 8px;
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
            <div class="alert-icon"></div>
            <h1>ALERT ADMIN - Donasi Besar!</h1>
        </div>

        <div class="alert-message">
            <p><strong>⚠️ PERHATIAN:</strong> Donasi besar baru saja diterima dan memerlukan perhatian khusus.</p>
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
                <span class="detail-label">🔍 Status:</span>
                <span class="detail-value urgent">BESAR - MEMERLUKAN VERIFIKASI</span>
            </div>
        </div>

        <div class="donor-info">
            <div class="section-title">Informasi Donatur</div>
            @if($donation->is_anonymous)
                <p><strong>👤 Nama:</strong> Anonim</p>
            @else
                <p><strong>👤 Nama:</strong> {{ $donor->name }}</p>
                <p><strong>📧 Email:</strong> {{ $donor->email }}</p>
                <p><strong>🆔 User ID:</strong> {{ $donor->id }}</p>
            @endif
            
            @if($donation->message)
            <p><strong>💬 Pesan:</strong></p>
            <p style="font-style: italic; margin-left: 20px;">"{{ $donation->message }}"</p>
            @endif
        </div>

        <div class="campaign-info">
            <div class="section-title">Campaign yang Didukung</div>
            <h4>🎯 {{ $campaign->title }}</h4>
            <div class="detail-row">
                <span class="detail-label">👤 Campaign Owner:</span>
                <span class="detail-value">{{ $campaignOwner->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">📧 Owner Email:</span>
                <span class="detail-value">{{ $campaignOwner->email }}</span>
            </div>
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

        <div class="action-steps">
            <div class="section-title">Tindakan yang Disarankan</div>
            <ol class="step-list">
                <li><strong>Verifikasi keabsahan donasi</strong> - Pastikan donasi berasal dari sumber yang legitimate</li>
                <li><strong>Periksa profil donatur</strong> - Jika tidak anonim, review profil dan riwayat donasi</li>
                <li><strong>Monitor campaign</strong> - Pantau campaign untuk donasi besar lainnya</li>
                <li><strong>Kirim ucapan terima kasih</strong> - Pertimbangkan untuk mengirim ucapan khusus</li>
                <li><strong>Update campaign owner</strong> - Informasikan tentang donasi besar ini</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('campaign.show', $campaign->slug) }}" class="cta-button">
                Lihat Campaign
            </a>
        </div>

        <div class="footer">
            <p><strong>Sistem Monitoring Beramal</strong></p>
            <p>Email ini dikirim secara otomatis untuk donasi besar (≥ Rp 1,000,000)</p>
            <p>Segera lakukan verifikasi dan tindakan yang diperlukan.</p>
        </div>
    </div>
</body>
</html>
