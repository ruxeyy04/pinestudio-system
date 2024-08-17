<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';

    $target_dir = "../img/services/";
    $file_basename = basename($_FILES["image_file"]["name"]);
    $file_extension = strtolower(pathinfo($file_basename, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image_file"]["tmp_name"]);
    if ($check !== false) {

        // Insert the food record without the image first to get the last inserted ID
        $stmt = $conn->prepare("INSERT INTO services (`name`, `type`, `description`, price, `image`) VALUES (?, ?, ?, ?, '')");
        $stmt->bind_param("ssss", $name, $type, $description, $price);

        if ($stmt->execute()) {
            // Get the last inserted ID
            $last_id = $stmt->insert_id;

            // Define new filename using the last inserted ID
            $new_filename = $last_id . "." . $file_extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                $image = $new_filename;

                // Update the record with the correct image filename
                $stmt = $conn->prepare("UPDATE services SET image = ? WHERE id = ?");
                $stmt->bind_param("si", $image, $last_id);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["success" => false, "message" => "Error updating image in database."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Sorry, there was an error uploading your file."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error inserting user into database."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "File is not an image."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
