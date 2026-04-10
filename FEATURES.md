# Fitur DonasiKita

Dokumentasi fitur-fitur spesifik aplikasi DonasiKita untuk crowdfunding dan donasi online.

## 🏠 Landing Page & Kampanye

### Halaman Utama
- **Daftar Kampanye**: Tampilan grid kampanye dengan filter dan pencarian
- **Kategori Kampanye**: Filter berdasarkan kategori donasi
- **Call-to-Action**: Tombol donasi yang menarik perhatian
- **Modal Donasi**: Popup form donasi yang user-friendly
- **Komponen UI**: Komponen siap pakai untuk landing page

### Halaman Kampanye
- **Detail Kampanye**: `/campaign/{slug}`
- **Galeri Foto**: Upload dan tampilan foto kampanye
- **Progress Bar**: Visualisasi target donasi
- **Deskripsi Lengkap**: Artikel kampanye dengan rich text
- **Donatur Terbaru**: Daftar donatur yang baru saja berdonasi

## 💰 Sistem Donasi

### Alur Donasi
- **Form Donasi**: `/campaign/{slug}/donate` (memerlukan login)
- **Pembayaran Midtrans Snap**: Integrasi redirect payment
- **Status Pembayaran**: Tracking real-time status donasi
- **Histori Donasi**: Riwayat donasi per pengguna
- **Notifikasi**: Email notifikasi untuk donasi berhasil

### Metode Pembayaran
- **Midtrans Snap**: Redirect ke halaman pembayaran Midtrans
- **Multiple Payment**: Credit card, bank transfer, e-wallet
- **Webhook Integration**: Auto-update status pembayaran
- **Callback Handling**: Redirect setelah pembayaran selesai

## 👥 Dashboard User

### Dashboard Pribadi
- **Dashboard**: `/dashboard` (memerlukan verifikasi email)
- **Riwayat Donasi**: Daftar semua donasi yang pernah dilakukan
- **Kampanye Favorit**: Bookmark kampanye yang disukai
- **Statistik Personal**: Grafik donasi dan aktivitas

## 🛠️ Panel Admin (Filament v4)

### Manajemen Data
- **Users Management**: Kelola pengguna, role, dan permission
- **Campaigns Management**: Buat, edit, arsip kampanye
- **Campaign Categories**: Kelola kategori kampanye
- **Donations Management**: Monitor dan kelola donasi
- **Attachments**: Upload dan kelola file lampiran

### Widget & Statistik
- **Dashboard Widgets**: Grafik dan statistik real-time
- **Revenue Tracking**: Tracking pendapatan donasi
- **User Analytics**: Analisis aktivitas pengguna
- **Campaign Performance**: Performa kampanye

### Fitur Admin
- **Role & Permission**: Sistem otorisasi berbasis Spatie
- **Bulk Actions**: Aksi massal untuk data
- **Export/Import**: Export data ke Excel/CSV
- **Audit Trail**: Log aktivitas admin

## 📎 Sistem Upload & Lampiran

### Attachment Management
- **Model Attachment**: Storage publik untuk file
- **File Upload**: Upload gambar, dokumen, dll
- **File Validation**: Validasi tipe dan ukuran file
- **Storage Link**: Akses publik ke file upload
- **Multiple File Support**: Upload beberapa file sekaligus

### File Types Support
- **Images**: JPG, PNG, GIF, WebP
- **Documents**: PDF, DOC, DOCX
- **Archives**: ZIP, RAR
- **Custom Types**: Konfigurasi tipe file custom

## 📧 Sistem Notifikasi

### Email Notifications
- **New Donation**: Notifikasi donasi baru
- **Payment Success**: Konfirmasi pembayaran berhasil
- **Campaign Updates**: Update status kampanye

## 🔄 Integrasi & API

### Third-party Integrations
- **Midtrans Payment**: Payment gateway integration
- **Webhook Support**: Midtrans webhook handling
- **API Authentication**: Token-based auth
