<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$query = "SELECT dep_id,dep_name FROM department";

$stmt = $conn->prepare($query);

//json paramas
$error = false;
$message = "success";
$result;

// $id = array();
// $name = array();
$department = array();

if($stmt->execute())
{
    $stmt->store_result();
    $stmt->bind_result($dep_id,$dep_name);
    $i = 0;
    
    while($stmt->fetch())
    {
        // $id[$i] = $dep_id;
        // $name[$i] = $dep_name;
        $department[$i++] = array("dep_id" => $dep_id,"dep_name" => $dep_name);
    }
    http_response_code(200);
    
    $result = array();
    $result["departments"] = $department;
    
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

$stmt->close();
$conn->close();
    
?>