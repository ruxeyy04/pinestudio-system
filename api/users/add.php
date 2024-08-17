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
        echo json_encode($response);
        exit();
    }

    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $age = $_POST["age"];
    $email = $_POST["email"];
    // $username = $_POST["username"];
    $password = $_POST["password"];
    $gender = $_POST["gender"];
    $address = $_POST["address"] ?? '';
    $usertype = $_POST["usertype"] ?? "Client";

    // Validate required fields
    foreach ($_POST as $key => $value) {
        if (empty($value)) {
            $response["error"]["empty"] = "All inputs are required";
            echo json_encode($response);
            exit();
        }
    }

    // Check if email already exists
    $query = "SELECT * FROM users WHERE email = ?";
    $sql = $conn->prepare($query);
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $response["error"]["email"] = "Email already exists";
        echo json_encode($response);
        exit();
    }

    // Insert user details into the database
    $query = "INSERT INTO users (firstname, lastname, age, email, gender, address, password, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $sql = $conn->prepare($query);
    $sql->bind_param("ssisssss", $firstname, $lastname, $age, $email, $gender, $address, $password, $usertype);
    $sql->execute();

    $userID = $conn->insert_id;

    // Handle file upload
    if (isset($_FILES['profileImage'])) {
        $fileTmpPath = $_FILES['profileImage']['tmp_name'];
        $fileName = $_FILES['profileImage']['name'];
        $fileSize = $_FILES['profileImage']['size'];
        $fileType = $_FILES['profileImage']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Use the user ID as the file name
        $newFileName = $userID . '.' . $fileExtension;

        // Directory where the uploaded file will be moved
        $uploadFileDir = '../../img/profiles/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Update the user's image path in the database
            $query = "UPDATE users SET image = ? WHERE id = ?";
            $sql = $conn->prepare($query);
            $sql->bind_param("si", $newFileName, $userID);
            $sql->execute();
        } else {
            $response["error"]["file_upload"] = "Failed to move uploaded file";
        }
    }

    $conn->commit();
    $response["success"] = true;
} catch (Exception $e) {
    $conn->rollback();
    $response["error"]["catch"] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
