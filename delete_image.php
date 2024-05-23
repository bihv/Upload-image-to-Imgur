<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accessToken = $_SESSION['access_token'];
    $deleteHash = $_POST['delete_hash'];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.imgur.com/3/image/$deleteHash",
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken"
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $responseData = json_decode($response, true);

    header('Content-Type: application/json');
    echo json_encode($responseData);
}
?>
