<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$pid = $_POST['pid'];

date_default_timezone_set('Asia/Kolkata');

$medicalRecords = null;

$query = "SELECT mid,pid,DOE,description,diagnosis,attachment FROM medical_records WHERE pid=? ORDER BY DOE DESC";

$stmt = $conn->prepare($query);

$stmt->bind_param("i",$pid);

if($stmt->execute())
{
    $stmt->store_result();
    $stmt->bind_result($mid,$pid,$timestamp,$description,$diagnosis,$attachment);

    $i = 0;
    while($stmt->fetch())
    {
        //making date
        $date = date('d-m-Y',$timestamp);

        $medicalRecords[$i++] = array(
            "mid" => $mid,
            "doe" => $date,
            "description" => $description,
            "diagnosis" => $diagnosis,
            "attachment" => $attachment
        );
    }
    $error = false;
    $message = "success";
    if($medicalRecords==null)
    {
        $medicalRecords = array();
    }
    $result['medicalRecords'] = $medicalRecords;
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