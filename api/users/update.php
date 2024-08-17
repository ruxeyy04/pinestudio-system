<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require "../database.php";

$response = array();

$conn->begin_transaction();

try {
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
  if ($_SERVER['REQUEST_METHOD'] != "PUT") {
    $response['message'] = "Invalid method";
    http_response_code(405);
    exit();
  }

  $putData = file_get_contents("php://input");
  $data = parse_multipart_formdata($putData);

  $response["data"] = $data;

  $userid = $data["userid"];
  $firstname = $data["firstname"];
  $lastname = $data["lastname"];
  $email = $data["email"];
  $gender = $data["gender"];
  $address = $data["address"];
  $password = $data["password"];
  $usertype = $data["usertype"] ?? "Client";
  $image = $data["image"];

  $response["data"] = $image;


  $sql = $conn->prepare("SELECT image FROM users WHERE id = ?");
  $sql->bind_param("i", $userid);
  $sql->execute();
  $result = $sql->get_result();
  $row = $result->fetch_assoc();
  $oldImage = $row["image"];

  $imageName = $oldImage;
  $imageUploaded = false;
  if ($image["name"] != null) {
    $imageUploaded = true;
    $imageName = time() . $image["name"];

    $allowedMimeTypes = ["image/png", "image/jpeg", "image/jpg", "image/gif"];
    $uploadedMimeType = $data["image"]["type"];

    if (!in_array($uploadedMimeType, $allowedMimeTypes)) {
      $response["error"]["image"] = "Invalid image extension";
      echo json_encode($response);
      exit();
    }
  }

  $query = "SELECT * FROM users WHERE email = ? AND id <> ?";
  $sql = $conn->prepare($query);
  $sql->bind_param("si", $email, $userid);
  $sql->execute();
  $result = $sql->get_result();
  if ($result->num_rows > 0) {
    $response["error"]["email"] = "Email already exists";
  }

  foreach ($data as $key => $value) {
    if (empty($value) && $key != "password") {
      $response["error"]["empty"] = "All inputs are required";
    }
  }

  if (isset($response["error"])) {
    echo json_encode($response);
    exit();
  }

  $query = "UPDATE users SET firstname = ?, lastname = ?, email = ?, gender = ?, address = ?, password = ?, image = ? WHERE id = ?";
  $sql = $conn->prepare($query);
  $sql->bind_param("sssssssi", $firstname, $lastname, $email, $gender, $address, $password, $imageName, $userid);
  $sql->execute();

  if ($imageUploaded) {
    $fileContentBase64 = base64_encode(file_get_contents($image["tmp_name"]));

    $uploadDirectory = "../../img/profiles/";
    $destination = $uploadDirectory . $imageName;

    $decodedContent = base64_decode($fileContentBase64);
    if (!file_put_contents($destination, $decodedContent)) {
      $response["error"]["image"] = "Failed to move uploaded file";
      echo json_encode($response);
      exit();
    }

    if ($oldImage != null) {
      $oldImagePath = $uploadDirectory . $oldImage;
      if (file_exists($oldImagePath)) {
        unlink($oldImagePath);
      }
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
