# Setup HTTPS untuk Development

## Option 1: Gunakan Laravel Valet (Windows)

```bash
composer global require cretueusebiu/valet-windows
valet install
valet secure pos-nativephp
```

## Option 2: Gunakan Laravel Herd

1. Download dari: https://herd.laravel.com/windows
2. Install dan add project folder
3. Otomatis dapat HTTPS

## Option 3: Generate Self-Signed Certificate Manual

```bash
# Generate certificate
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout localhost.key -out localhost.crt

# Jalankan dengan HTTPS
php artisan serve --host=localhost --port=8000 \
  --ssl-cert=localhost.crt --ssl-key=localhost.key
```

## Option 4: Allow Insecure Downloads (Quick Fix)

**Chrome/Edge:**

1. Klik icon 🔒 atau ⚠️ di address bar
2. Site settings
3. Insecure content → Allow

**Note:** Option 4 hanya untuk development, jangan untuk production!
