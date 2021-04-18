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
$t = $_POST["time"];
//

//converting time into day,month,year,hour,minute,second
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
$second = $time[5];

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
    
    //getting doctor and patient details for message
    $doctorQuery = 'select phone from doctor where doc_id=?';
    $doctorStmt = $conn->prepare($doctorQuery);
    $doctorStmt->bind_param('i',$doc_id);
    $doctorStmt->execute();
    $doctorStmt->store_result();
    $doctorStmt->bind_result($phone);
    $doctorStmt->fetch();
    
    
    $patientQuery = 'select name from Patient where pid=?';
    $patientStmt = $conn->prepare($patientQuery);
    $patientStmt->bind_param('i',$pid);
    $patientStmt->execute();
    $patientStmt->store_result();
    $patientStmt->bind_result($name);
    $patientStmt->fetch();
    
    //sending message to doctor
    $bookingMessage = 'Patient name: '.$name.' has booked an appointment for '.$t;
    
    $to = '+91'.$phone;
    
    include 'sms.php';
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