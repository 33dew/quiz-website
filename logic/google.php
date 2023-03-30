<?php


namespace Google;

use Google_Client;

class Auth {
    static $client;
    static function createClient($clientId, $clientSecret, $redirectUri) {
        self::$client = new Google_Client();
        self::$client->setClientId($clientId);
        self::$client->setClientSecret($clientSecret);
        self::$client->setRedirectUri($redirectUri);
        self::$client->addScope("email");
        self::$client->addScope("profile");
    }

    static function getAuthLink() {
        $auth_url =self::$client->createAuthUrl();
        return $auth_url;
    }
}