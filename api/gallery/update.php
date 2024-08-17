<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require "../database.php";

  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
  }
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$putData = file_get_contents("php://input");
$formData = parse_multipart_formdata($putData);

// Debugging: print the formData content
file_put_contents('php://stderr', print_r($formData, true));

$gallery_id = $formData['gallery_id'] ?? '';
$name = $formData['editName'] ?? '';
$serviceID = $formData['editServiceGroup'] ?? '';

if (empty($gallery_id) || empty($name) || empty($serviceID)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

try {
    // Update the gallery table
    $updateGallery = "UPDATE gallery SET serviceID = ?, name = ? WHERE id = ?";
    $stmtUpdateGallery = $conn->prepare($updateGallery);
    $stmtUpdateGallery->bind_param("isi", $serviceID, $name, $gallery_id);

    if (!$stmtUpdateGallery->execute()) {
        throw new Exception("Failed to update gallery item");
    }

    if (isset($formData['image'])) {
        // Get the existing image path
        $query = "SELECT image FROM gallery WHERE id = ?";
        $stmtSelect = $conn->prepare($query);
        $stmtSelect->bind_param("i", $gallery_id);
        $stmtSelect->execute();
        $stmtSelect->bind_result($existingImagePath);
        $stmtSelect->fetch();
        $stmtSelect->close();

        if ($existingImagePath && file_exists($existingImagePath)) {
            unlink($existingImagePath); // Delete the existing image file
        }

        $file = $formData['image'];
        if (is_array($file) && isset($file['tmp_name'], $file['name'], $file['type'])) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileType = $file['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Generate a unique name for the file based on the gallery ID
            $newFileName = $gallery_id . '.' . $fileExtension;

            $fileContentBase64 = base64_encode(file_get_contents($fileTmpPath));

            // Create the image file in the profile directory
            $uploadDirectory = realpath(__DIR__ . "/../../img/gallery/") . DIRECTORY_SEPARATOR;
            $dest_path = $uploadDirectory . $newFileName;
            // Decode base64 content and save it to the file
            $decodedContent = base64_decode($fileContentBase64);
            // Move the uploaded file to the destination directory
            if (!file_put_contents($dest_path, $decodedContent)) {
                throw new Exception("Failed to move uploaded file");
            }

            // Update the gallery table with the new image path
            $updateImage = "UPDATE gallery SET image = ? WHERE id = ?";
            $stmtUpdateImage = $conn->prepare($updateImage);
            $stmtUpdateImage->bind_param("si", $newFileName, $gallery_id);

            if (!$stmtUpdateImage->execute()) {
                throw new Exception("Failed to update gallery item image");
            }
        } else {
            throw new Exception("Invalid file data");
        }
    }

    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Gallery item updated successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

