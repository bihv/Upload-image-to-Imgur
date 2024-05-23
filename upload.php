<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accessToken = $_SESSION['access_token'];
    $image = $_FILES['image']['tmp_name'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $handle = fopen($image, "r");
    $data = fread($handle, filesize($image));
    fclose($handle);

    $pvars = array(
        'image' => base64_encode($data),
        'title' => $title,
        'description' => $description
    );

    $timeout = 30;
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image');
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);

    $json_returned = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($json_returned, true);

    if ($response['success']) {
        header("Location: gallery.php");
        exit();
    } else {
        echo "Error uploading image to Imgur.";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Upload Image to Imgur</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
    <body>
    <div class="container mt-5">
        <h1 class="text-center">Upload Image to Imgur</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label for="image" class="form-label">Choose an image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="/gallery.php" class="btn btn-info">Manage Image</a>
        </form>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>
