<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$query = "SELECT dep_id,dep_name FROM department";

$stmt = $conn->prepare($query);
$stmt->bind_param();

//json paramas
$error = false;
$message = "success";
$result;

$id = array();
$name = array();

if($stmt->execute())
{
    $stmt->store_result();
    $stmt->bind_result($dep_id,$dep_name);
    $i = 0;
    while($stmt->fetch())
    {
        $id[$i] = $dep_id;
        $name[$i] = $dep_name;
        $i++;
    }
    http_response_code(200);
    
    $result = array(
        "dep_id" => $id,
        "dep_name" => $name);
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
