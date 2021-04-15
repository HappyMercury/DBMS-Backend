<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

//post params
$pid = $_POST['pid'];
$doc_id = $_POST['doc_id'];
$slot = $_POST['slot'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$second = $_POST['second'];
$day = $_POST['day'];
$month = $_POST['month'];
$year = $_POST['year'];
//

$query = "INSERT INTO appointment (pid,doc_id,timestamp,slot) VALUES (?,?,?,?)";

date_default_timezone_set('Asia/Kolkata');

$timestamp = mktime($hour,$minute,$second,$month,$day,$year);//converting dateTime to unix timestamp

$dateTime = date("d-m-Y h-i a",$timestamp);

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii",$pid,$doc_id,$timestamp,$slot);
if($stmt->execute())//if query successfull
{
    $error = false;
    $message = "success";
    $result["appointment"] = array("pid" => $pid, "doc_id" => $doc_id, "dateTime" => $dateTime, "slot" => $slot);
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