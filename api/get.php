<?php

include 'config.php';

$chefs = array();

$query = "SELECT * FROM services";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        

        $chefs[] = $row;
    }
} else {
    echo json_encode(array());
    exit();
}

$conn->close();

echo json_encode($chefs);
