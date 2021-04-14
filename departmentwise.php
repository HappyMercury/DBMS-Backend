<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$dep_id = $_POST["dep_id"];

//variables for json response
$error = false;
$message = "success";
$result;

$query = "SELECT doc_id,phone,name,email,auth,dnum,hnum,hname,image,street,area FROM doctor,hospital where dnum=? AND hnum=hid";

$stmt = $conn->prepare($query);
$stmt->bind_param("i",$dep_id);

if($stmt->execute())
{
    
    $stmt->store_result();
    $stmt->bind_result($doc_id,$phone,$name,$email,$auth,$dnum,$hnum,$hname,$image,$street,$area);

    $result = array();
    $index = 0;
    
    $depNameQuery = "SELECT dep_name FROM department WHERE dep_id=?";
    $depNamestmt = $conn->prepare($depNameQuery);
    $depNamestmt->bind_param("i",$dep_id);
    $depNamestmt->execute();
    $depNamestmt->store_result();
    $depNamestmt->bind_result($dep_name);
    $depNamestmt->fetch();

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
        "hospital" => array("hid" => $hnum,"hname" => $hname,"street" => $street,"area" => $area),
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