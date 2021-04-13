<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$query = 'SELECT hid,hname,street,area FROM hospital';

$stmt = $conn->prepare($query);

if($stmt->execute())
{

    $stmt->bind_result($hid,$hname,$street,$area);

    $result = array();
    $hospital = array();
    $index = 0;

    //stores the list of hospitals in array
    while($stmt->fetch())
    {
        $hospital[$index++] = array("hid" => $hid,"hname" => $hname,"street" => $street,"area" => $area);
    }
    $result["hospitals"] = $hospital;
    $error = false;
    $message = "success";
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