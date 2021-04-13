<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$email = $_POST['email'];
$password = md5($_POST['password']);


//writing sql query
$query = 'SELECT doc_id,phone,name,age,address,email,auth,dnum,hnum FROM Patient WHERE email=? AND password=?';


//making and executing prepared statement
$stmt = $conn->prepare($query);
$stmt->bind_param("ss",$email,$password);
$stmt->execute();

$stmt->store_result();

$stmt->bind_result($doc_id,$phone,$name,$age,$address,$email,$auth,$dnum,$hnum);

//variables for json response
$error = null;
$message = null;
$result = null;

if($stmt->num_rows>0)
{
	$stmt->fetch();

	//getting hospital and department name
	$query = "SELECT hname FROM hospital WHERE hnum=".$hnum;
	$queryResult = $conn->query($query);
	$row = mysqli_fetch_assoc($queryResult);
	$hospital = $row['hname'];

	$query = "SELECT dname FROM department WHERE dnum=".$dnum;
	if($queryResult = $conn->query($query))
    {
        $row = mysqli_fetch_assoc($result);
	    $department = $row['dname'];

        //sending data to user in json
        $error = false;
        $message = "success";
        $result = array(
            'doc_id' =>$doc_id,
            'name' => $name,
            'phone' => $phone,
            'age' => $age,
            'address' => $address,
            'email' => $email,
            'auth' => $auth,
            'hospital' => $hospital,
            'department' => $department
            );

    }
    http_response_code(200);

}
else {
	//unauthorized user
    $error = true;
    $message = "failure";
    $result = array();
    http_response_code(500);
}

$data = array(
    "error" => $error,
    "message" => $message,
    $result => $result
    );

$json = json_encode($data,JSON_FORCE_OBJECT);

echo $json;

$stmt->close();
$conn->close();

?>