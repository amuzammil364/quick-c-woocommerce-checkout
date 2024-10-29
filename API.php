<?php
class API_Handler
{
    private $api_url;

    public function __construct($url)
    {
        $this->api_url = $url;
    }


    public function registerUser($first_name, $last_name, $email, $primary_contact, $secondary_contact, $addresses, $delivery_preferences, $root_domain)
    {
        $data = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "profile" => array(
                "primary_contact" => $primary_contact,
                "secondary_contact" => $secondary_contact,
            ),
            "addresses" => $addresses,
            "delivery_preferences" => $delivery_preferences,
            "domain" => $root_domain
        );

        $json_data = json_encode($data);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response, true);
        return $response_data;
    }

    public function authenticate($email, $domain, $platform, $verifyMethod)
    {

        $data = array(
            "user" => $email,
            "domain" => $domain,
            "platform" => $platform,
            "verify_method" => $verifyMethod
        );

        $json_data = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        $response_data = json_decode($response, true);

        return $response_data;
    }

    public function verify_authenticate($user, $verifyMethod, $otp, $token)
    {

        $data = array(
            "user" => $user,
            "verify_method" => $verifyMethod,
            "value" => $otp,
        );

        $json_data = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        $response_data = json_decode($response, true);


        return $response_data;
    }

    public function registerPlatForm($name, $domain, $description, $ip)
    {
        $data = array(
            "name" => $name,
            "domain" => $domain,
            "meta" => array(
                "description" => $description,
                "ip" => $ip
            )
        );

        $json_data = json_encode($data);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response);
        return $response_data;
    }

    public function checkApiKey($token)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: JWT ' . $token
        ]);


        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response, true);
        return $response_data;
    }

    public function getShortAddresses($short_address)
    {

        $url = sprintf(
            '%s?short_address=%s',
            $this->api_url,
            urlencode($short_address),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response, true);
        return $response_data;
    }

    public function getUserDetail($token, $email)
    {

        $url = sprintf(
            '%s?email=%s',
            $this->api_url,
            urlencode($email),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $token . '',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response, true);
        return $response_data;
    }

    public function createOrder($api_key, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $api_key . '',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response, true);
        return $response_data;
    }

    public function updateOrder($api_key, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $api_key . '',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);

        $response_data = json_decode($response);
        return $response_data;
    }
}
