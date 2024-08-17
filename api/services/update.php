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

$services_id = $formData['services_id'] ?? '';
$name = $formData['name'] ?? '';
$price = $formData['price'] ?? null;
$description = $formData['description'] ?? '';
$type = $formData['type'] ?? null;
if (empty($services_id) || empty($name) || empty($price) || empty($description)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

try {
    // Update the gallery table
    $updateServices = "UPDATE services SET description = ?, type= ?, name = ?, price = ? WHERE id = ?";
    $stmtUpdateServices = $conn->prepare($updateServices);
    $stmtUpdateServices->bind_param("sssii", $description, $type, $name, $price, $services_id);

    if (!$stmtUpdateServices->execute()) {
        throw new Exception("Failed to update gallery item");
    }

    if (isset($formData['image'])) {
        // Get the existing image path
        $query = "SELECT image FROM services WHERE id = ?";
        $stmtSelect = $conn->prepare($query);
        $stmtSelect->bind_param("i", $services_id);
        $stmtSelect->execute();
        $stmtSelect->bind_result($existingImagePath);
        $stmtSelect->fetch();
        $stmtSelect->close();

        // Remove the associated image file
        $uploadDirectory = '../../img/services/';
        $imagePath = $uploadDirectory . $existingImagePath;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }


        $file = $formData['image'];
        if (is_array($file) && isset($file['tmp_name'], $file['name'], $file['type'])) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileType = $file['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Generate a unique name for the file based on the gallery ID
            $newFileName = $services_id . '.' . $fileExtension;

            $fileContentBase64 = base64_encode(file_get_contents($fileTmpPath));

            // Create the image file in the profile directory
            $uploadDirectory = realpath(__DIR__ . "/../../img/services/") . DIRECTORY_SEPARATOR;
            $dest_path = $uploadDirectory . $newFileName;
            // Decode base64 content and save it to the file
            $decodedContent = base64_decode($fileContentBase64);
            // Move the uploaded file to the destination directory
            if (!file_put_contents($dest_path, $decodedContent)) {
                throw new Exception("Failed to move uploaded file");
            }

            // Update the gallery table with the new image path
            $updateImage = "UPDATE services SET image = ? WHERE id = ?";
            $stmtUpdateImage = $conn->prepare($updateImage);
            $stmtUpdateImage->bind_param("si", $newFileName, $services_id);

            if (!$stmtUpdateImage->execute()) {
                throw new Exception("Failed to update gallery item image");
            }
        } else {
            throw new Exception("Invalid file data");
        }
    }

    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Service item updated successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

