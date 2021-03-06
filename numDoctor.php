<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

//json params;
$error = false;
$message = "success";
$result;

$query = "SELECT dep_id,dep_name,count(*) FROM department INNER JOIN doctor ON dep_id=dnum GROUP BY dep_id";

$stmt = $conn->prepare($query);
$stmt->bind_param();

if($stmt->execute())
{
    $stmt->store_result();
    $stmt->bind_result($dep_id,$dep_name,$count);
    
    $i = 0;
    
    while($stmt->fetch())
    {
        $result[$i++] = array($dep_id,$dep_name,$count);
    }
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

$json = json_encode($data,JSON_FORCE_OBJECT);
echo $json;

$stmt->close();
$conn->close();

?>
