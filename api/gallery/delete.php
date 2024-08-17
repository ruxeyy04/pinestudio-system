<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require "../database.php";

// Check if the request method is DELETE
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
  }
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

// Get the raw DELETE data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['gallery_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$gallery_id = $data['gallery_id'];

try {
    // First, retrieve the image path to delete the file from the server
    $selectQuery = "SELECT image FROM gallery WHERE id = ?";
    $stmtSelect = $conn->prepare($selectQuery);
    $stmtSelect->bind_param("i", $gallery_id);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Gallery item not found"]);
        exit;
    }

    $row = $result->fetch_assoc();
    $imagePath = $row['image'];

    // Delete the record from the database
    $deleteQuery = "DELETE FROM gallery WHERE id = ?";
    $stmtDelete = $conn->prepare($deleteQuery);
    $stmtDelete->bind_param("i", $gallery_id);

    if ($stmtDelete->execute()) {
        // Delete the image file from the server
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Gallery item deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to delete gallery item"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
