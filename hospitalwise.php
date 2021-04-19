<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hid = $_POST["hid"];

//variables for json response
$error = false;
$message = "success";
$result;

$query = "SELECT doc_id,phone,name,email,auth,dnum,hnum,image,dep_name FROM doctor,department where dnum=dep_id AND hnum=?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i",$hid);

if($stmt->execute())
{
    
    $stmt->store_result();
    $stmt->bind_result($doc_id,$phone,$name,$email,$auth,$dnum,$hnum,$image,$dep_name);

    $result = array();
    $index = 0;
    
    //stores the list of hospitals in array
    while($stmt->fetch())
    {
        $doctors[$index++] = array(
        "doc_id" => $doc_id,
        "phone" => $phone,
        "name" => $name,
        "email" => $email,
        "auth" => $auth,
        "image" => $image,
        "department" => array("dep_id" => $dnum,"dep_name" => $dep_name)
        );
    }
    $result["doctors"] = $doctors;
    http_response_code(200);
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