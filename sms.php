<?php
// Include the bundled autoload from the Twilio PHP Helper Library
require __DIR__ . '/twilio-php-main/src/Twilio/autoload.php';
use Twilio\Rest\Client;
// Your Account SID and Auth Token from twilio.com/console
$account_sid = '';
$auth_token = '';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]
// A Twilio number you own with SMS capabilities

$client = new Client($account_sid, $auth_token);

    $twilio_number = "";
    $client->messages->create(
        // Where to send a text message (your cell phone?)
        $to,
        array(
            'from' => $twilio_number,
            'body' => $bookingMessage
        )
);
