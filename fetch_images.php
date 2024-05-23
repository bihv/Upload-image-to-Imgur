<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit();
}

$accessToken = $_SESSION['access_token'];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.imgur.com/3/account/me/images",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken"
    ]
]);

$response = curl_exec($curl);
curl_close($curl);

$responseData = json_decode($response, true);

if (isset($responseData['data'])) {
    $images = $responseData['data'];
} else {
    $images = [];
}

foreach ($images as $image) {
    $title = !empty($image['title']) ? htmlspecialchars($image['title']) : '&nbsp;';
    echo '
    <div class="col-md-2 mb-4">
        <div class="card">
            <img src="' . htmlspecialchars($image['link']) . '" class="card-img-top" alt="Uploaded Image">
            <div class="card-body">
                <span class="card-title text-center">' . $title . '</span>
                <div class="d-flex justify-content-between">
                    <a href="' . htmlspecialchars($image['link']) . '" target="_blank" class="btn btn-primary mx-1" data-bs-toggle="tooltip" title="View Full Size">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </a>
                    <button class="btn btn-secondary mx-1" onclick="copyToClipboard(\'' . htmlspecialchars($image['link']) . '\')" data-bs-toggle="tooltip" title="Copy URL">
                        <i class="bi bi-clipboard"></i>
                    </button>
                    <button class="btn btn-danger mx-1" onclick="showDeleteModal(\'' . htmlspecialchars($image['deletehash']) . '\')" data-bs-toggle="tooltip" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>';
}
?>
