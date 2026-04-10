# DonasiKita

Starter kit donasi/crowdfunding berbasis Laravel 12 dengan Livewire, Filament Admin, dan Midtrans Snap.

## Prasyarat

- PHP 8.2+
- Composer
- Node.js 18+ dan npm
- Database: SQLite (default) atau MySQL/PostgreSQL

## Instalasi Cepat

1. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

2. **Setup environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Setup database**
   - **SQLite (default):**
     ```bash
     touch database/database.sqlite
     ```
   - **MySQL/PostgreSQL:** Edit `.env` dengan konfigurasi database Anda

4. **Migrate dan seed**

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

5. **Jalankan aplikasi**
   ```bash
   composer dev
   ```

## Akses Aplikasi

- **Frontend:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin
- **Dashboard:** http://localhost:8000/dashboard

## Akun Default

- **Email:** superadmin@example.com
- **Password:** example

## Konfigurasi Midtrans (Opsional)

Tambahkan ke `.env` untuk fitur pembayaran:

```env
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

## Testing

```bash
composer test
```
