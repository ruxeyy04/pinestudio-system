<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $putData = file_get_contents("php://input");
    $formData = parse_multipart_formdata($putData);

    $response = [];
    $id = $formData['id'] ?? '';
    $name = $formData['name'] ?? '';
    $type = $formData['type'] ?? '';
    $description = $formData['description'] ?? '';
    $price = $formData['price'] ?? '';
    $imageFilename = '';
    $file = $formData['image_file'] ?? '';
    if (isset($formData['image_file']) && $file['name'] != null && $file['name'] != 'empty.jpg') {
        // Fetch the current image filename
        $stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($currentImage);
        $stmt->fetch();
        $stmt->close();

        // Delete the old image file
        if ($currentImage && file_exists("../img/services/" . $currentImage)) {
            unlink("../img/services/" . $currentImage);
        }
        $response['Uploaded file name'] = $file['name'];
        $response['Uploaded file type'] = $file['type'];

        $fileContentBase64 = base64_encode(file_get_contents($file['tmp_name']));

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageFilename = $id . '.' . $file_extension;
        
        $uploadDirectory = realpath(__DIR__ . "../../img/services/") . DIRECTORY_SEPARATOR;
        $destination = '../img/services/' . $imageFilename;

        $decodedContent = base64_decode($fileContentBase64);
        if (file_put_contents($destination, $decodedContent)) {
            $response['file_upload_status'] = "success";
            $response['file_destination'] = $destination;
        } else {
            $response['file_upload_status'] = "error";
        }
    }

    // Construct SQL query
    $sql = "UPDATE services SET `name`=?, `type`=?, description=?, price=?";
    $params = [$name, $type, $description, $price];

    if (!empty($imageFilename)) {
        $sql .= ", image=?";
        $params[] = $imageFilename;
    }

    $sql .= " WHERE id=?";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to update data information';
    }

    $stmt->close();

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>