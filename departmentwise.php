<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$dep_id = $_POST["dep_id"];

//variables for json response
$error = false;
$message = "success";
$result;

$query = "SELECT doc_id,phone,name,age,address,email,auth,dnum,hnum,hname FROM doctor,hospital where dnum=? AND hnum=hid";

$stmt = $conn->prepare($query);
$stmt->bind_param("i",$dep_id);

if($stmt->execute())
{

    $stmt->bind_result($doc_id,$phone,$name,$age,$address,$email,$auth,$dnum,$hnum,$hname);

    $result = array();
    $index = 0;

    //stores the list of hospitals in array
    while($stmt->fetch())
    {
        $result[$index++] = array("doc_id" => $doc_id,
        "phone" => $phone,
        "name" => $name,
        "age" => $age,
        "address" => $address,
        "email" => $email,
        "auth" => $auth,
        "dep_id" => $dnum,
        "hid" => $hnum,
        "hname" => $hname);
    }
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