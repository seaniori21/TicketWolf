<?php
$api_url = "https://api.twilio.com/2010-04-01/Accounts/your_account_sid/Messages.json";
$api_data = [
    'To' => '+1234567890',
    'From' => 'your_twilio_number',
    'Body' => 'Hello! This is a test SMS.'
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "your_account_sid:your_auth_token");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_data));
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>