<?php

include 'Database.php';

$db = new Database();
$conn = $db->connect();

$image = $_POST["image"];
$profession = $_POST["profession"];
$id = $_POST["id"];

//json params
$error = true;
$message = "failure";
$result = (object)[];

if($profession=="doctor")//if doctor
{
    $query = "UPDATE doctor SET image=? WHERE doc_id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si",$image,$id);
    if($stmt->execute())
    {
        $error = false;
        $message = "success";
        $result = (object) [];
    }
}
else if($profession=="patient")//if patient
{
    $query = "UPDATE Patient SET image=? WHERE pid=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si",$image,$id);
    if($stmt->execute())
    {
        $error = false;
        $message = "success";
        $result = (object) [];
    }
}

$data = array(
    "error" => $error,
    "message" => $message,
    "result" => $result
    );

$json = json_encode($data);

echo $json;

$conn->close();
?>