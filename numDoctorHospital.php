<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

//json params;
$error = false;
$message = "success";
$result;

$query = "SELECT hid,hname,street,area,count(*) FROM hospital,doctor WHERE hid=hnum GROUP BY hid";

$stmt = $conn->prepare($query);

if($stmt->execute())
{
    $stmt->store_result();
    $stmt->bind_result($hid,$hname,$street,$area,$count);
    
    $i = 0;
    
    $result = array();

    while($stmt->fetch())
    {
        $doctorHospital[$i++] = array("hid" => $hid,"hname" => $hname,"street" => $street,"area" => $area,"count" => $count);
    }
    $result['doctorHospital'] = $doctorHospital;
    http_response_code(200);
}
else
{
    $error = true;
    $message = "failure";
    $result = array();
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