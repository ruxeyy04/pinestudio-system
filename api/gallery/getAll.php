<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "../database.php";

$response = array();

$conn->begin_transaction();

try {
  if ($_SERVER['REQUEST_METHOD'] != "GET") {
    $response['message'] = "Invalid method";
    http_response_code(405);
    exit();
  }

  $query = "SELECT 
              gallery.*,
              services.name AS servicename,
              services.type
            FROM gallery
            INNER JOIN services
            ON gallery.serviceid = services.id";
  $sql = $conn->prepare($query);
  $sql->execute();
  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $row["image"] = $row["image"] ?? "default.png";
      $response["data"][] = $row;
    }
  } else {
    $response["data"] = array();
  }

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
