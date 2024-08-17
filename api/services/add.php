<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require "../database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

// Check if all required fields are provided
if (!isset($_POST['serviceName'], $_POST['servicePrice'], $_POST['serviceDescription'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$serviceName = $_POST['serviceName'];
$servicePrice = $_POST['servicePrice'];
$serviceDescription = $_POST['serviceDescription'];
$serviceType  = $_POST['serviceType'];


// Insert service details into the database
$insertServiceQuery = "INSERT INTO services (name, price, description, type) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertServiceQuery);
$stmt->bind_param("ssss", $serviceName, $servicePrice, $serviceDescription, $serviceType);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to add service to the database"]);
    exit;
}

// Get the insert ID of the service
$serviceID = $stmt->insert_id;
if(isset($_FILES['image'])){
    // Handle image upload
    $image = $_FILES['image'];
    $uploadDirectory = '../../img/services/';
    $targetFile = $uploadDirectory . basename($image['name']);
    
    // Check if the image file is valid
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if ($image['size'] > 5000000) { // 5MB limit
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Image size exceeds the limit"]);
        exit;
    }
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Only JPG, JPEG, PNG, and GIF files are allowed"]);
        exit;
    }
    // Use the service ID as the file name
    $newFileName = $serviceID . '.' . $imageFileType;
    $dest_path = $uploadDirectory . $newFileName;
    
    // Move the uploaded image to the destination directory
    if (!move_uploaded_file($image['tmp_name'], $dest_path)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to move uploaded file"]);
        exit;
    }
    
    // Update the service record with the image file name
    $updateServiceQuery = "UPDATE services SET image = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($updateServiceQuery);
    $stmtUpdate->bind_param("si", $newFileName, $serviceID);
    
    if (!$stmtUpdate->execute()) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to update service image"]);
        exit;
    }
}


http_response_code(200);
echo json_encode(["status" => "success", "message" => "Service added successfully"]);

