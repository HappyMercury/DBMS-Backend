<?php

include 'Database.php';

$db = new Database();
$conn = $db->connect();

date_default_timezone_set('Asia/Kolkata');

$pid = $_POST['pid'];
$t = $_POST['time'];

$tok = strtok($t, " :-");
$i = 0;
while ($tok !== false) 
{
    $time[$i++] = $tok;
    $tok = strtok(" :-");
}

$day =  $time[0];
$month = $time[1];
$year = $time[2];
$hour =  $time[3];
$minute = $time[4];

$timestamp = mktime($hour,$minute,0,$month,$day,$year);//converting dateTime to unix timestamp

$query = "SELECT pid from appointment where pid=? and timestamp=?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii',$pid,$timestamp);
$stmt->execute();

if($stmt->num_rows==0)
{
    $error = false;
    $message = "success";
    $result = (object) [];
    http_response_code(200);
}
else
{
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

$conn->close();

?>