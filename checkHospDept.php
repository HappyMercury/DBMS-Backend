<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hname = $_POST['hname'];
$dep_name = $_POST['dep_name'];

$query = "select hnum,dnum from hosp_dept where hnum=(select hid from hospital where hname=?) and dnum=(select dep_id from department where dep_name=?)";

$stmt = $conn->prepare($query);
$stmt->bind_param('ss',$hname,$dep_name);
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