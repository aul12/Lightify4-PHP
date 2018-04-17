<?php
class LightifyConnection {
    public static $locale = "emea"; // alternative: na for north america

    public static function getLoginUrl($clientId, $redirectUrl) {
        $state = rand(10000, 99999);
        return "https://".LightifyConnection::$locale.".lightify-api.com/oauth2/authorize?".
            "client_id=".$clientId.
            "&state=".$state.
            "&redirect_uri=".$redirectUrl.
            "&response_type=code";
    }


    private $clientId, $clientSecret, $redirectUrl;
    private $accessToken, $refreshToken;

    public function __construct($clientId, $clientSecret, $redirectUrl) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
    }

    public function generateToken($code) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".LightifyConnection::$locale.".lightify-api.com/oauth2/access_token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>
                "client_id=".$this->clientId.
                "&client_secret=".$this->clientSecret.
                "&code=".$code.
                "&grant_type=authorization_code&redirect_uri=".$this->redirectUrl,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("cUrl Error: ".$err);
            return false;
        } else {
            $result = json_decode($response);
            $this->accessToken = $result->{"access_token"};
            $this->refreshToken = $result->{"refresh_token"};
            return true;
        }
    }

    function renewToken() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".LightifyConnection::$locale.".lightify-api.com/oauth2/access_token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "client_id=".$this->clientId.
                                "&client_secret=".$this->clientSecret.
                                "&refresh_token=".$this->refreshToken.
                                "&grant_type=refresh_token",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("cUrl Error: ".$err);
            return false;
        } else {
            $result = json_decode($response);
            $this->accessToken = $result->{"access_token"};
            $this->refreshToken = $result->{"refresh_token"};
            return true;
        }
    }



    public function listDevices() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".LightifyConnection::$locale.".lightify-api.com/v4/devices/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$this->accessToken
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("cUrl Error: ".$err);
            return null;
        } else {
            return json_decode($response);
        }
    }

    public function setDevice($deviceId, $state) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".LightifyConnection::$locale.".lightify-api.com/v4/devices/".$deviceId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => "{ \"onOff\" : \"".($state?"on":"off")."\" }",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$this->accessToken,
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("cUrl Error: ".$err);
            return null;
        } else {
            return json_decode($response);
        }
    }
}