<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hours = $db->hours;

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
$flag = $_POST['flag'];
//

$timestamp = mktime($hour,$minute,$second,$month,$day,$year);//converting dateTime to unix timestamp

//update Query
$updateQuery = "UPDATE appointment (pid,doc_id,timestamp,slot) SET (?,?,?,?) WHERE pid=? AND doc_id=? AND timestamp=?";

$deleteQuery = "DELETE FROM appointment WHERE pid=? AND doc_id=? AND timestamp=?";

//json params
$error = true;
$message = "failure";
$result = null;

if($flag==0)//update
{
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("iii",$pid,$doc_id,$timestamp);
    if($updateStmt->execute())//if query successfull
    {
        $error = false;
        $message = "success";
        $result = (object) [];
        http_response_code(200);
    }
    else
    {
        http_response_code(200);
    }
}
else if($flag==1)
{
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("iii",$pid,$doc_id,$timestamp);
    if($deleteStmt->execute())//if query successfull
    {
        $error = false;
        $message = "success";
        $result = (object) [];
        http_response_code(200);
    }
    else
    {
        http_response_code(200);
    }
}
else
{
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