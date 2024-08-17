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
// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

// Get the JSON data from the request body
$input = json_decode(file_get_contents("php://input"), true);

// Check if the service ID is provided
if (!isset($input['userid'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Service ID is required"]);
    exit;
}

$userid = $input['userid'];

// Fetch the service image filename from the database
$stmt = $conn->prepare("SELECT image FROM users WHERE id = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Service not found"]);
    exit;
}

$stmt->bind_result($imageFilename);
$stmt->fetch();
$stmt->close();

// Delete the service from the database
$deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->bind_param("i", $userid);

if (!$deleteStmt->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to delete service from database"]);
    exit;
}

// Remove the associated image file
$uploadFileDir = '../../img/profiles/';
$imagePath = $uploadFileDir . $imageFilename;
if ($imageFilename != null && file_exists($imagePath)) {
    unlink($imagePath);
}

http_response_code(200);
echo json_encode(["status" => "success", "message" => "Service deleted successfully"]);