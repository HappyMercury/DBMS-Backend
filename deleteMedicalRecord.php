<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$mid = $_POST['mid'];

$query = "DELETE FROM medical_records WHERE mid=?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i",$mid);
if($stmt->execute())
{
    $error = false;
    $message = "success";
    $result = (object) [];
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

$data = array(
    "error" => $error,
    "message" => $message,
    "result" => $result
);

$json = json_encode($data);

echo $json;

$stmt->close();
$conn->close();

?>