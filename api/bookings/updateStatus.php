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
  if ($_SERVER['REQUEST_METHOD'] != "PUT") {
    $response['message'] = "Invalid method";
    http_response_code(405);
    exit();
  }
  
  $putData = file_get_contents("php://input");
  $data = parse_multipart_formdata($putData);

  $bookingid = $data["bookingid"];
  $status = $data["status"];

  $query = "UPDATE bookings SET status = ? WHERE id = ?";
  $sql = $conn->prepare($query);
  $sql->bind_param("si", $status, $bookingid);
  $sql->execute();

  $conn->commit();
  $response["success"] = true;
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
