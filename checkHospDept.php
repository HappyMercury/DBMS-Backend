<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hid = $_POST['hid'];
$dep_id = $_POST['dep_id'];

$query = "select hnum,dnum from hosp_dept where hnum=? and dnum=?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii',$hid,$dep_id);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)// already exists
{
    $error = true;
    $message='failure';
    $result = null;
    http_response_code(200);
}
else
{
    $error = false;
    $message='success';
    $result = (object)[];
    http_response_code(200);
}

$data = array(
    'error' => $error,
    'message' => $message,
    'result' => $result
);

$json = json_encode($data);

echo $json;

$conn->close();

?>