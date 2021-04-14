<?php



include 'Database.php';



$db = new Database();

$conn = $db->connect();



//varibales from user

$email = $_POST["email"];

$password = md5($_POST["password"]);



//result paramters

$error = false;

$message = "success";

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

	$patient = array(

	    'profession' => "patient",

		'pid' => $pid,

		'phone' => $phone,

		'name' => $name,

		'age' => $age,

		'address' => $address,

		'email' => $email,

		'auth' => $auth

	);

    $result['patient'] = $patient;



}

else

{

    $query = "SELECT doc_id,phone,name,email,auth,dnum,hnum FROM doctor WHERE email=? AND password=?";





    //executing doctor query

    $docStmt = $conn->prepare($query);

    $docStmt->bind_param("ss",$email,$password);

    $docStmt->execute();

    $docStmt->store_result();

    $docStmt->bind_result($doc_id,$phone,$name,$email,$auth,$dnum,$hnum);



    if($docStmt->num_rows>0)//if exists in doctor

    {

        $docStmt->fetch();



        //getting hospital and department name

        $query = "SELECT hname FROM hospital WHERE hid=?";



        $hospStmt = $conn->prepare($query);

        $hospStmt->bind_param("i",$hnum);

        $hospStmt->execute();

        $hospStmt->store_result();

        $hospStmt->bind_result($hospital);

        $hospStmt->fetch();



        $query = "SELECT dep_name FROM department WHERE dep_id=?";

        

        $depStmt = $conn->prepare($query);

        $depStmt->bind_param("i",$dnum);

        

        if($depStmt->execute())

        {

            $depStmt->store_result();

            $depStmt->bind_result($department);

            $depStmt->fetch();



            //sending data to user in json

            $error = false;

            $message = "success";

            $doctor = array(

                'profession' => "doctor",

                'doc_id' =>$doc_id,

                'name' => $name,

                'phone' => $phone,

                'email' => $email,

                'auth' => $auth,

                'hid' => $hnum,

                'hname' => $hospital,

                'dep_id' => $dnum,

                'dep_name' => $department

                );

                $result['doctor'] = $doctor;

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

}



$data = array(

    "error" => $error,

    "message" => $message,

    "result" => $result

    );



$json = json_encode($data);



echo $json;



// $stmt->close();

$conn->close();



?>