<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require "../database.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

// Check if the necessary data is available
if (!isset($_FILES['addImage'], $_POST['serviceID'], $_POST['name'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$serviceID = $_POST['serviceID'];
$name = $_POST['name'];

try {
    // Insert into gallery table
    $insertGallery = "INSERT INTO gallery (serviceID, name) VALUES (?, ?)";
    $stmtGallery = $conn->prepare($insertGallery);
    $stmtGallery->bind_param("is", $serviceID, $name);

    if ($stmtGallery->execute()) {
        $galleryID = $conn->insert_id;

        $fileTmpPath = $_FILES['addImage']['tmp_name'];
        $fileName = $_FILES['addImage']['name'];
        $fileSize = $_FILES['addImage']['size'];
        $fileType = $_FILES['addImage']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Use the gallery ID as the file name
        $newFileName = $galleryID . '.' . $fileExtension;

        // Directory where the uploaded file will be moved
        $uploadFileDir = '../../img/gallery/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the uploaded file to the destination directory
        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to move uploaded file"]);
            exit;
        }

        // Update the gallery table with the image path
        $updateGallery = "UPDATE gallery SET image = ? WHERE id = ?";
        $stmtUpdateGallery = $conn->prepare($updateGallery);
        $stmtUpdateGallery->bind_param("si", $dest_path, $galleryID);

        if ($stmtUpdateGallery->execute()) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Gallery item added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to update gallery item"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to add gallery item"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
