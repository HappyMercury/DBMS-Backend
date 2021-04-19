<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$email = $_POST['email'];
$password = md5($_POST['password']);
$auth = $_POST['auth'];

if($auth==0)//custom authentication
{

    $query = "(select email from Patient where email=? and password=?) union (select email from doctor where email=? and password=?)";
    $emailCheckStmt = $conn->prepare($query);
    $emailCheckStmt->bind_param('ssss',$email,$password,$email,$password);
}
else//google login checking only ewmail
{
    $query = "(select email from Patient where email=?) union (select email from doctor where email=?)";
    $emailCheckStmt = $conn->prepare($query);
    $emailCheckStmt->bind_param('ss',$email,$email);
}


$emailCheckStmt->execute();
$emailCheckStmt->store_result();

if($emailCheckStmt->num_rows>0)//email already exists
{
    $error = true;
    $message='failure';
    $result = null;
    http_response_code(200);
}
else
{
    $error = false;
    $message='success';
    $result = (object)[];
    http_response_code(200);
}

$data = array(
    'error' => $error,
    'message' => $message,
    'result' => $result
);

$json = json_encode($data);

echo $json;

$conn->close();

?>