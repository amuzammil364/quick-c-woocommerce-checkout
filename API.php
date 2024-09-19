<?php
class API_Handler
{
    private $api_url;

    public function __construct($url)
    {
        $this->api_url = $url;
    }

    public function authenticate($email)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email]));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        $response_data = json_decode($response, true);

        return $response_data['success'] ? $response_data : false;
    }
}
