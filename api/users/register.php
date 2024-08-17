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

  $firstname = $_POST["firstname"];
  $lastname = $_POST["lastname"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $gender = $_POST["gender"];
  $address = $_POST["address"];
  $usertype = $_POST["usertype"] ?? "Client";

  $query = "SELECT * FROM users WHERE email = ?";
  $sql = $conn->prepare($query);
  $sql->bind_param("s", $email);
  $sql->execute();
  $result = $sql->get_result();
  if ($result->num_rows > 0) {
    $response["error"]["email"] = "Email already exists";
  }

  foreach ($_POST as $key => $value) {
    if (empty($value)) {
      $response["error"]["empty"] = "All inputs are required";
    }
  }

  if (isset($response["error"])) {
    echo json_encode($response);
    exit();
  }

  $query = "INSERT INTO users (firstname, lastname, email, gender, address, password, usertype) VALUES (?, ?, ?, ?, ?, ?, ?)";
  $sql = $conn->prepare($query);
  $sql->bind_param("sssssss", $firstname, $lastname, $email, $gender, $address, $password, $usertype);
  $sql->execute();

  $conn->commit();

  $response["success"] = true;
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
