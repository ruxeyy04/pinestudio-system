<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require "../database.php";

$response = array();

$conn->begin_transaction();

try {
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] != "DELETE") {
    $response['message'] = "Invalid method";
    http_response_code(405);
    exit();
  }

  // Retrieve the booking ID from the request body
  $data = json_decode(file_get_contents("php://input"), true);
  $bookingId = $data['bookingId'];
  // Prepare and execute the DELETE query
  $query = "DELETE FROM bookings WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $bookingId);
  $stmt->execute();

  // Check if any rows were affected
  if ($stmt->affected_rows > 0) {
    $response['status'] = "success";
  } else {
    $response['status'] = "error";
  }

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();

echo json_encode($response);

