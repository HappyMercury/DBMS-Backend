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

if($stmt->num_rows>0)
{

	http_response_code(200);
	$stmt->fetch();
	//authorized user
	// $data = array(
	// 	'pid' => $row['pid'],
	// 	'name' => $row['name'],
	// 	'age' => $row['age'],
	// 	'email' => $row['email'],
	// 	'address' => $row['address']
	// );

	$data = array(
		'pid' => $pid,
		'phone' => $phone,
		'name' => $name,
		'age' => $age,
		'address' => $address,
		'email' => $email,
		'auth' => $auth
	);

	$json = json_encode($data);

	echo $json;

}
else {
	//unauthorized user
	http_response_code(401);
}

?>

