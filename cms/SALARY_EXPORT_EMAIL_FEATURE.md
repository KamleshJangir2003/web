# Salary Excel Export & Email Feature

## Features Added

### 1. Excel Download
- Month-wise salary data export to Excel
- Includes all employee details: Name, Email, Designation, Department, Shift, Working Days, Salary breakdown
- Button: "Download Excel" on salary page

### 2. Email Salary Slips
- Select multiple employees using checkboxes
- Send salary slips via email to selected employees
- Beautiful HTML email template with salary breakdown
- Button: "Send Emails" on salary page

## Files Created/Modified

### New Files:
1. `app/Exports/SalaryExport.php` - Excel export class
2. `app/Mail/SalarySlipMail.php` - Email mailable class
3. `resources/views/emails/salary-slip.blade.php` - Email template

### Modified Files:
1. `app/Http/Controllers/Admin/SalaryController.php` - Added exportExcel() and sendEmail() methods
2. `routes/web.php` - Added routes for export and email
3. `resources/views/admin/salary/index.blade.php` - Added UI buttons and checkboxes

## Usage

### Download Excel:
1. Go to `/admin/salary`
2. Select month and year
3. Click "Download Excel" button
4. Excel file will download automatically

### Send Emails:
1. Go to `/admin/salary`
2. Select month and year
3. Check the employees you want to send emails to
4. Click "Send Emails" button
5. Confirm the action
6. Emails will be sent to selected employees

## Routes Added:
- GET `/admin/salary/export/excel` - Download Excel
- POST `/admin/salary/send-email` - Send emails

## Email Configuration Required:
Make sure your `.env` file has proper email configuration:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Package Installed:
- maatwebsite/excel (for Excel export)
- Already using Laravel Mail (for emails)
