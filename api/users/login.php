<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "../database.php";

$response = array();

$conn->begin_transaction();

try {
  if ($_SERVER['REQUEST_METHOD'] != "POST") {
    $response['message'] = "Invalid method";
    http_response_code(405);
    exit();
  }

  $email = $_POST["email"];
  $password = $_POST["password"];

  $query = "SELECT * FROM users WHERE email = ? AND password = ?";
  $sql = $conn->prepare($query);
  $sql->bind_param("ss", $email, $password);
  $sql->execute();
  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $response["success"] = true;
    $response["data"] = $row;
  } else {
    $response["error"]["invalid"] = "Invalid credentials";
  }

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
