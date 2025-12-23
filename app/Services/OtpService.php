<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class OtpService
{
    private $apiKey = '';



    private $apiUrl = 'https://www.traccar.org/sms/';

    /* ⬅️ إعدادات Apple */
    private string $appleReviewPhone = '+15555550123';
    private string $appleStaticOtp   = '12345';


    public function attemptSendOtp(string $phone, string $otp): bool
    {
        try {
            // Apple: لا ترسل SMS، اعتبرها نجحت
            if ($phone === $this->appleReviewPhone) {
                Log::channel('single')->info('[APPLE REVIEW] attemptSendOtp bypassed (no real SMS).', [
                    'phone' => $phone,
                    'otp'   => $otp,
                ]);
                return true;
            }

            $message = "كود التحقق الخاص بك هو: $otp. يرجى عدم مشاركته مع أي شخص.";
            Log::channel('single')->info('[SMS][OUTBOUND][attemptSendOtp] Preparing to send OTP SMS.', [
                'to'      => $phone,
                'message' => $message,
            ]);

            $postData = json_encode([
                'to'      => $phone,
                'message' => $message
            ]);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postData,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: ' . $this->apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSLVERSION     => CURL_SSLVERSION_DEFAULT,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);
            curl_close($ch);

            Log::channel('single')->info('[SMS][OUTBOUND][attemptSendOtp] HTTP Response', [
                'to'          => $phone,
                'status_code' => $httpCode,
                'body'        => $response,
            ]);

            if ($error) {
                Log::channel('single')->error('[SMS][OUTBOUND][attemptSendOtp] cURL Error', [
                    'to'    => $phone,
                    'error' => $error,
                ]);
                return false;
            }

            $ok = $httpCode >= 200 && $httpCode < 300;

            Log::channel('single')->info('[SMS][OUTBOUND][attemptSendOtp] Result', [
                'to'     => $phone,
                'status' => $ok ? 'sent' : 'failed',
            ]);

            return $ok;
        } catch (\Exception $e) {
            Log::channel('single')->error('SMS sending error: ' . $e->getMessage(), [
                'to' => $phone,
            ]);
            return false;
        }
    }
}
