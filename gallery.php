<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit();
}

$accessToken = $_SESSION['access_token'];

function fetchImages($accessToken) {
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
        return $responseData['data'];
    } else {
        return [];
    }
}

$images = fetchImages($accessToken);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Images Gallery</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <style>
        .card-img-top {
            width: 100%;
            height: 200px; /* Chiều cao cố định cho ảnh */
            object-fit: cover; /* Giữ tỷ lệ ảnh */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card-body .card-title {
            margin-bottom: 15px;
            text-align: center;
            width: 100%;
        }
        .card-body .btn-group {
            width: 100%;
        }
        .card-body .btn-group .btn {
            flex: 1;
        }
        .copy-success {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1001;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <h1 class="text-center">Uploaded Images Gallery</h1>
    <div id="image-gallery" class="row">
        <!-- Image gallery will be populated by JavaScript -->
    </div>
    <div class="mt-4">
        <a href="upload.php" class="btn btn-secondary">Upload More Images</a>
    </div>
</div>

<div class="alert alert-success copy-success" id="copySuccessAlert">URL copied to clipboard!</div>
<div class="spinner-border text-primary spinner" id="spinner" role="status">
    <span class="sr-only">Loading...</span>
</div>

<!-- Modal for delete confirmation -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this image?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Yes</button>
            </div>
        </div>
    </div>
</div>


<script>
    let deleteHashToDelete = ''
    function fetchImages() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_images.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('image-gallery').innerHTML = xhr.responseText;
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            }
        };
        xhr.send();
    }

    function copyToClipboard(url) {
        navigator.clipboard.writeText(url).then(function() {
            let copySuccessAlert = document.getElementById('copySuccessAlert');
            copySuccessAlert.style.display = 'block';
            setTimeout(function() {
                copySuccessAlert.style.display = 'none';
            }, 2000);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    function showDeleteModal(deleteHash) {
        deleteHashToDelete = deleteHash;
        $('#confirmDeleteModal').modal('show');
    }

    document.getElementById('confirmDeleteButton').addEventListener('click', function() {
        const spinner = document.getElementById('spinner');
        spinner.style.display = 'block';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_image.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            spinner.style.display = 'none';
            if (xhr.status === 200) {
                $('#confirmDeleteModal').modal('hide');
                fetchImages();
            } else {
                alert('Error deleting image from Imgur.');
            }
        };
        xhr.send('delete_hash=' + encodeURIComponent(deleteHashToDelete));
    });

    document.addEventListener('DOMContentLoaded', fetchImages);
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
