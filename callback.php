<?php
session_start();

$clientId = 'YOUR_CLIENT_ID';  // Thay bằng Client ID của bạn
$clientSecret = 'YOUR_CLIENT_SECRET';  // Thay bằng Client Secret của bạn

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.imgur.com/oauth2/token",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code
        ]),
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $responseData = json_decode($response, true);

    if (isset($responseData['access_token'])) {
        $_SESSION['access_token'] = $responseData['access_token'];
        header('Location: gallery.php');
        exit();
    } else {
        echo 'Error getting access token';
    }
} else {
    echo 'No code provided';
}
?>
