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

$type = $_POST["type"];
$address = $_POST["address"];
$startdate = $_POST["startdate"];
$enddate = $_POST["enddate"];
$serviceid = $_POST["serviceid"];
$userid = $_POST["userid"];
$status = $_POST["status"] ?? "Pending";

$currentDateTime = new DateTime();
$startDateTime = new DateTime($startdate);
$endDateTime = new DateTime($enddate);

// Compare dates including time
if ($startDateTime < $currentDateTime) {
    $response["error"]["startdate"] = "Start date must not be before current date and time";
}

if ($endDateTime <= $startDateTime) {
    $response["error"]["enddate"] = "End date must be after your start date";
}

// Check for minimum 5 hours difference
$interval = $startDateTime->diff($endDateTime);
$hoursDifference = ($interval->days * 24) + $interval->h + ($interval->i / 60);

if ($hoursDifference < 5) {
    $response["error"]["enddate"] = "End date must be at least 5 hours after start date";
}


  if (!isset($response["error"])) {
    $conn->begin_transaction();
    
    try {
      $query = "INSERT INTO bookings (userid, serviceid, type, address, startdate, enddate, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $sql = $conn->prepare($query);
      $sql->bind_param("iisssss", $userid, $serviceid, $type, $address, $startdate, $enddate, $status);
      $sql->execute();
  
      $conn->commit();
      $response["success"] = true;
    } catch (Exception $e) {
      $conn->rollback();
      $response["error"]["catch"] = $e->getMessage();
    }
  }

  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
  $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
