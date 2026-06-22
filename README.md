# ویزیت ایرانیان (visitIranian)

پلتفرم معرفی پزشکان، رزرو نوبت آنلاین، پنل مدیریت و پنل پزشک — با Laravel و MySQL.

## نیازمندی‌ها

- PHP 8.2+
- MySQL 8+
- Composer
- Node.js 20+

## نصب سریع

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### دیتابیس (یکی از دو روش)

**روش ۱ — SQL مستقیم:**

```bash
mysql -u root -p < database/sql/schema.sql
mysql -u root -p visitiranian < database/sql/procedures.sql
```

**روش ۲ — Migrations لارavel:**

```bash
# در .env اتصال MySQL را تنظیم کنید
php artisan migrate --seed
```

### Frontend

```bash
npm install
npm run build
php artisan storage:link
```

### اجرا

```bash
php artisan serve
php artisan queue:work
php artisan schedule:work
```

## آدرس‌ها

| بخش | URL |
|-----|-----|
| سایت عمومی | `/` |
| پنل مدیریت | `/admin` |
| پنل پزشک | `/doctor` |
| پیگیری نوبت | `/peygiri` یا `/n/{code}` |

## حساب پیش‌فرض (بعد از seed)

- ایمیل: مقدار `ADMIN_EMAIL` در `.env` (پیش‌فرض `admin@example.com`)
- رمز: `ADMIN_PASSWORD` (پیش‌فرض `password`)

## تنظیمات مهم `.env`

- `KAVENEGAR_API_KEY` / `KAVENEGAR_SENDER` — پیامک
- `VISITIRANIAN_DEVELOPER_EMAIL` — دسترسی به تنظیمات توسعه‌دهنده (Telegram/Bale)
- `SUPPORT_TELEGRAM_*` / `SUPPORT_BALE_*` — اعلان ticket پشتیبانی

## Sitemap

```bash
php artisan sitemap:generate
```

## تست

```bash
php artisan test
```
