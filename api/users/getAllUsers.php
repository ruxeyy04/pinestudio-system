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

  $query = "SELECT * FROM users WHERE usertype = 'Client'";
  $sql = $conn->prepare($query);
  $sql->execute();
  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as &$row) {
      // Ensure the "image" key exists with a default value if it doesn't exist
      $row["image"] = $row["image"] ?? "default.jpg";
    }
    $response["data"] = $rows;
  }

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
