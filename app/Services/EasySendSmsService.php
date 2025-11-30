<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class EasySendSmsService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('easysendsms.api_key');
        $this->baseUrl = config('easysendsms.base_url', 'https://restapi.easysendsms.app/v1/rest');

        if (empty($this->apiKey)) {
            throw new Exception('EasySendSMS API key is not configured.');
        }
    }

    /**
     * Query HLR (Home Location Register) for a given phone number.
     *
     * @param string $phoneNumber The phone number to query.
     * @return array|null The API response data as an array, or null on failure.
     * @throws Exception If the API key is not configured or an unexpected error occurs.
     */
    public function queryHlr(string $phoneNumber): ?array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/hlr/query", [
                'number' => $phoneNumber,
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("EasySendSMS HLR query failed with status {$response->status()}: " . $response->body());
                throw new Exception("EasySendSMS HLR query failed.");
            }
        } catch (Exception $e) {
            Log::error("EasySendSMS HLR query exception: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Send a single SMS message.
     *
     * @param string $to The recipient's phone number.
     * @param string $text The message content.
     * @param string $from The sender name (alpha or numeric).
     * @param int $type The message type (0 for plain text, 1 for Unicode).
     * @return array|null The API response data, or null on failure.
     * @throws Exception If the API request fails.
     */
   public function sendSms(string $to, string $text, string $from = 'MyClinic', int $type = 0): ?array // ⬅️ FIXED: Added $type parameter with default value
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/sms/send", [
                "from" =>  $from,
                "to" => '+237'.$to,
                "text" => $text,
                "type" => (string) $type // ⬅️ FIXED: Used $type variable instead of hardcoded "0"
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("EasySendSMS send SMS failed with status {$response->status()}: " . $response->body());
                throw new Exception("EasySendSMS send SMS failed.");
            }
        } catch (Exception $e) {
            Log::error("EasySendSMS send SMS exception: {$e->getMessage()}");
            throw $e;
        }
    }
}
