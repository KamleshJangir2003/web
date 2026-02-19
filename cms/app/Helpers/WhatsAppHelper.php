<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    public static function sendJoiningLetter($employee)
    {
        if (!$employee->phone) {
            return false;
        }

        $message = self::formatJoiningMessage($employee);
        
        // Example for WATI.io / Generic WhatsApp API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('WHATSAPP_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('WHATSAPP_API_URL'), [
                'phone' => self::formatPhoneNumber($employee->phone),
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    private static function formatJoiningMessage($employee)
    {
        $name = $employee->full_name;
        $jobTitle = $employee->job_title ?? 'Employee';
        $joiningDate = $employee->joining_date ? $employee->joining_date->format('jS F Y') : 'N/A';
        $ctc = number_format($employee->current_ctc ?? 0, 0);

        return "Dear *{$name}*,\n\n"
            . "Greetings from *Kwikster Innovative Optimisations Pvt Ltd*.\n\n"
            . "We are pleased to confirm your appointment as *{$jobTitle}* with Kwikster, effective from *{$joiningDate}*.\n\n"
            . "Your Annual Total CTC will be *₹{$ctc}*, as discussed and agreed upon.\n\n"
            . "A detailed Joining Letter has been sent to your email: {$employee->email}\n\n"
            . "We are delighted to welcome you to the Kwikster team!\n\n"
            . "Best Regards,\n"
            . "HR Team\n"
            . "Kwikster Innovative Optimisations Pvt Ltd\n"
            . "📧 hr@thekwikster.com\n"
            . "📞 +91 96805 80889";
    }

    private static function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present
        if (strlen($phone) == 10) {
            $phone = '91' . $phone;
        }
        
        return $phone;
    }
}
