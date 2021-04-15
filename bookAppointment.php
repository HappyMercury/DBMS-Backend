<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hours = array(0,13,14,15,16,17,18,0,0,9,10,11,12);

//post params
$pid = $_POST['pid'];
$doc_id = $_POST['doc_id'];
$slot = $_POST['slot'];
$hour = $hours[$slot];
$minute = $_POST['minute'];
$second = $_POST['second'];
$day = $_POST['day'];
$month = $_POST['month'];
$year = $_POST['year'];
//

$query = "INSERT INTO appointment (pid,doc_id,timestamp,slot) VALUES (?,?,?,?)";

date_default_timezone_set('Asia/Kolkata');

$timestamp = mktime($hour,$minute,$second,$month,$day,$year);//converting dateTime to unix timestamp

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii",$pid,$doc_id,$timestamp,$slot);
if($stmt->execute())//if query successfull
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