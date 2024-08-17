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
  $status = isset($_GET["status"]) ? $_GET["status"] : null; // Get status from the request
    $query = "SELECT 
                bookings.*, 
                bookings.type AS bookingtype, 
                services.name, 
                services.type AS servicetype, 
                services.price, 
                services.image, 
                users.firstname AS user_firstname, 
                users.lastname AS user_lastname, 
                users.email AS user_email 
              FROM bookings 
              INNER JOIN services ON bookings.serviceid = services.id 
              INNER JOIN users ON bookings.userid = users.id";
      if ($status) {
    $query .= " WHERE bookings.status = '$status'";
  }
  
    $sql = $conn->prepare($query);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $startdate = new DateTime($row["startdate"]);
            $enddate = new DateTime($row["enddate"]);
            $row["startdate"] = $startdate->format('F j, Y g:i A');
            $row["enddate"] = $enddate->format('F j, Y g:i A');
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
