<?php

include 'Database.php';

$db = new Database();
$conn = $db->connect();

//varibales from user
$email = $_POST["email"];
$password = md5($_POST["password"]);

//result paramters
$error;
$message;
$result;

//first checking if present in patient
$query = "SELECT pid,phone,name,age,address,email,auth FROM Patient WHERE email=? AND password=?";

//executing the patient query
$stmt = $conn->prepare($query);
$stmt->bind_param("ss",$email,$password);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($pid,$phone,$name,$age,$address,$email,$auth);

if($stmt->num_rows>0)//if exists in patient
{
    
    $stmt->fetch();

    //sending data to user in json
    $error = false;
    $message = "success";
	$result = array(
	    'profession' => "patient",
		'pid' => $pid,
		'phone' => $phone,
		'name' => $name,
		'age' => $age,
		'address' => $address,
		'email' => $email,
		'auth' => $auth
	);

}
else
{
    $query = "SELECT doc_id,phone,name,age,address,email,auth,dnum,hnum FROM doctor WHERE email=? AND password=?";


    //executing doctor query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss",$email,$password);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($doc_id,$phone,$name,$age,$address,$email,$auth,$dnum,$hnum);

    if($stmt->num_rows>0)//if exists in doctor
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
                'profession' => "doctor",
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
    else
    {
        $error = true;
        $message = "failure";
        $result = array();
        http_response_code(200);
    }
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
