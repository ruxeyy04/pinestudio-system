<?php

include 'config.php';

// Retrieve the search query from the request, if available
$search_query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

// Initialize the array to hold the results
$chefs = array();

// Construct the SQL query with a WHERE clause if a search query is provided
if ($search_query != '') {
    $query = "SELECT * FROM services 
              WHERE name LIKE '%$search_query%' 
              OR type LIKE '%$search_query%' 
              OR description LIKE '%$search_query%' 
              OR price LIKE '%$search_query%'";
} else {
    $query = "SELECT * FROM services";
}

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

?>
