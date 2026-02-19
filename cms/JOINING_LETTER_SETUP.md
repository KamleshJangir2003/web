# Joining Letter - Email & WhatsApp Setup

## ✅ Features Implemented

When admin selects an employee from `/admin/employees/hired`:
- ✅ **Email** - Joining letter sent to employee's email
- ✅ **WhatsApp** - Joining letter sent to employee's phone number

---

## 📧 Email Configuration (Already Working)

Your `.env` file already has email configured:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hr@thekwikster.com
MAIL_PASSWORD="mpid mhmr ovaz ppke"
MAIL_ENCRYPTION=tls
```

---

## 📱 WhatsApp Setup (Required)

### Option 1: WATI.io (Recommended - Easy Setup)

1. Sign up at https://www.wati.io/
2. Get your API credentials
3. Update `.env`:
```env
WHATSAPP_API_KEY=your-wati-api-key
WHATSAPP_API_URL=https://live-server-XXXX.wati.io/api/v1/sendSessionMessage
```

4. Update `WhatsAppHelper.php` line 23-28:
```php
$response = Http::withHeaders([
    'Authorization' => env('WHATSAPP_API_KEY'),
])->post(env('WHATSAPP_API_URL'), [
    'whatsappNumber' => self::formatPhoneNumber($employee->phone),
    'text' => $message,
]);
```

### Option 2: Twilio WhatsApp API

1. Sign up at https://www.twilio.com/
2. Get Account SID and Auth Token
3. Update `.env`:
```env
WHATSAPP_API_KEY=your-twilio-auth-token
WHATSAPP_API_URL=https://api.twilio.com/2010-04-01/Accounts/YOUR_ACCOUNT_SID/Messages.json
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

4. Update `WhatsAppHelper.php` accordingly

### Option 3: Interakt / Gupshup

Similar setup - get API credentials and update helper accordingly.

---

## 🚀 Installation Steps

1. Run composer autoload:
```bash
composer dump-autoload
```

2. Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

3. Test the feature:
   - Go to `/admin/employees/hired`
   - Select an employee with action_status = "selected"
   - Check employee's email and WhatsApp

---

## 📝 Message Format

### Email
Professional HTML email with:
- Employee name
- Job title
- Joining date
- CTC amount
- Company details

### WhatsApp
Formatted text message with:
- Employee name (bold)
- Job title (bold)
- Joining date (bold)
- CTC amount (bold)
- Email reference
- HR contact details

---

## 🔧 Troubleshooting

### Email not sending?
- Check `.env` mail configuration
- Verify Gmail app password is correct
- Check `storage/logs/laravel.log` for errors

### WhatsApp not sending?
- Verify WhatsApp API credentials in `.env`
- Check if phone number format is correct (10 digits)
- Check `storage/logs/laravel.log` for errors
- Test API endpoint manually using Postman

---

## 📂 Files Modified/Created

1. ✅ `app/Mail/JoiningLetterMail.php` - Email class
2. ✅ `app/Helpers/WhatsAppHelper.php` - WhatsApp helper
3. ✅ `resources/views/emails/joining-letter.blade.php` - Email template
4. ✅ `app/Http/Controllers/EmployeeDocumentController.php` - Controller updated
5. ✅ `composer.json` - Autoload updated

---

## 💡 Notes

- Email will always attempt to send
- WhatsApp will only send if phone number exists
- Both failures are logged but won't stop the process
- Success message shows which channels succeeded
