<?php

include('config.php');
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $putData = file_get_contents("php://input");

    $formData = parse_multipart_formdata($putData);
    $item_id = $formData['id'] ?? '';
    if (empty($item_id)) {
        echo json_encode(['success' => false, 'message' => 'item_id ID is required']);
        exit();
    }
    
    // Find the image file name
    $stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    if ($image) {
        // Set the directory where images are stored
        $target_dir = "../img/services/";
        $target_file = $target_dir . $image;

        // Delete the image file
        if (file_exists($target_file)) {
            unlink($target_file);
        } else {
            echo json_encode(['success' => false, 'message' => 'Image file does not exist']);
            exit();
        }

        // Delete the record from the database
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $item_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete data']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
