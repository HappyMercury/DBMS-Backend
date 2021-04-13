<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$email = $_POST['email'];
$password = md5($_POST['password']);


//writing sql query
$query = 'SELECT pid,phone,name,age,address,email,auth FROM Patient WHERE email=? AND password=?';


//making and executing prepared statement
$stmt = $conn->prepare($query);
$stmt->bind_param("ss",$email,$password);
$stmt->execute();

$stmt->store_result();

$stmt->bind_result($pid,$phone,$name,$age,$address,$email,$auth);

//variables for json response
$error = null;
$message = null;
$result = null;

if($stmt->num_rows>0)
{
	$stmt->fetch();

    //sending data to user in json
    $error = false;
    $message = "success";
	$result = array(
		'pid' => $pid,
		'phone' => $phone,
		'name' => $name,
		'age' => $age,
		'address' => $address,
		'email' => $email,
		'auth' => $auth
	);

    http_response_code(200);

}
else {
	//unauthorized user
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