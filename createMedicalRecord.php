<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$pid = $_POST['pid'];
$date = $_POST['date'];
$attachment = $_POST['attachment'];
$diagnosis = $_POST['diagnosis'];
$description = $_POST['description'];

date_default_timezone_set('Asia/Kolkata');

//converting time into day,month,year,hour,minute,second
$tok = strtok($date, "-");
$i = 0;
while ($tok !== false) 
{
    $time[$i++] = $tok;
    $tok = strtok("-");
}

$day =  $time[0];
$month = $time[1];
$year = $time[2];

$timestamp = mktime(0,0,0,$month,$day,$year);


$query = "INSERT INTO medical_records (pid,DOE,description,diagnosis,attachment) VALUES (?,?,?,?,?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("iisss",$pid,$timestamp,$description,$diagnosis,$attachment);
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