<?php
$ch = curl_init("https://api.sms.net.bd/sendsms");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if(curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Success: ' . $response;
}
curl_close($ch);
