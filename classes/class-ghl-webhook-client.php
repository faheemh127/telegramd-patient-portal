<?php

if (! defined('ABSPATH')) {
    exit;
}

class GhlApiClient
{
    private $apiKey;
    private $apiBaseUrl = "https://services.leadconnectorhq.com/";

    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException("GHL API Key cannot be empty.");
        }
        $this->apiKey = $apiKey;
    }
    public function createContact(array $contactData)
    {
        $contactDataUrl = $this->apiBaseUrl . 'contacts/';

        if (empty($contactData['email']) && empty($contactData['phone'])) {
            throw new InvalidArgumentException("Contact data must include at least an 'email' or 'phone' field.");
        }

        return $this->makeApiRequest('POST', $contactDataUrl, $contactData, "2021-07-28");
    }

    //This was my first thought to use the webhook, but after reading I found out that using webhook posts additional
    // charges on the users so I abandoned it. This is here just because I have written it before.
    public static function sendToWebhook(string $webhookUrl, array $data)
    {
        if (!filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid webhook URL provided.");
        }

        $jsonData = json_encode($data);
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ];

        $result = self::executeCurlRequest($webhookUrl, 'POST', $jsonData, $headers);

        if ($result['httpCode'] >= 300) {
            throw new Exception("HTTP Error (Webhook): {$result['httpCode']}. Response: " . $result['body']);
        }

        if ($result['body'] === "OK" || $result['body'] === "") {
            return ['status' => 'success', 'message' => 'Data sent successfully.'];
        }

        $decodedResponse = json_decode($result['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode webhook JSON response: " . json_last_error_msg());
        }

        return $decodedResponse;
    }

    private function makeApiRequest(string $method, string $url, array $data = [], $version = "")
    {
        $jsonData = json_encode($data);
        $headers = [
            'Authorization: Bearer ' . $this->apiKey, // <-- The key difference
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ];

        if ($version !== "") {
            $headers['Version'] = $version;
        }

        $result = self::executeCurlRequest($url, $method, $jsonData, $headers);

        $decodedResponse = json_decode($result['body'], true);

        if ($result['httpCode'] < 200 || $result['httpCode'] >= 300) {
            $errorMessage = $decodedResponse['message'] ?? 'Unknown API error';
            throw new Exception("HTTP Error (API): {$result['httpCode']}. Message: {$errorMessage}. Full Response: " . $result['body']);
        }

        if (json_last_error() !== JSON_ERROR_NONE && !($result['httpCode'] >= 200 && $result['httpCode'] < 300)) {
            throw new Exception("Failed to decode API JSON response: " . json_last_error_msg());
        }

        return $decodedResponse;
    }

    private static function executeCurlRequest(string $url, string $method, string $jsonData, array $headers)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $jsonData,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $responseBody = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Connection Error: " . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'httpCode' => $httpCode,
            'body'     => $responseBody
        ];
    }
}


// $GhlApiClient = new GhlApiClient(api_key);
// $data = [
//     "firstName"   => "Rosan",
//     "lastName"    => "Deo",
//     "name"        => "Rosan Deo",
//     "email"       => "rosan@deos.com",
//     "locationId"  => "tqGhhCGePHa1hQkrrOQY",
//     "gender"      => "male",
//     "phone"       => "+1 888-888-8888",
//     "address1"    => "3535 1st St N",
//     "city"        => "Dolomite",
//     "state"       => "AL",
//     "postalCode"  => "35061",
//     "website"     => "https://www.tesla.com",
//     "timezone"    => "America/Chihuahua"
// ];
// // (object) $data;
// $GhlApiClient->createContact($data);
