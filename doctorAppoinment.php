<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

//post params
$doc_id = $_POST['doc_id'];

//json params
$error = false;
$message = "success";
$result = array();

date_default_timezone_set('Asia/Kolkata');

$hour24 = 86400;

$currentDay = (int) date('d');
$currentYear = (int) date('Y');
$currentMonth = (int) date('m');

$currentTime = time();

$day1 = mktime(0, 0, 0, $currentMonth, $currentDay,$currentYear) + $hour24;
$day2 = $day1 + $hour24;
$day3 = $day2 + $hour24;
$day4 = $day3 + $hour24;

$result['doc_id'] = $doc_id;

$query1 = "SELECT slot FROM doc_slots WHERE doc_id=?";

//getting all slots
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("i",$doc_id);

if($stmt1->execute())
{
    $stmt1->store_result();
    $stmt1->bind_result($slot1);
    
    $i=0;

    while($stmt1->fetch())
    {
        $slots1[$i++] = $slot1;
    }
}

//today
$query2 = "SELECT slot FROM appointment where doc_id=? AND timestamp>? AND timestamp<?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("iii",$doc_id,$currentTime,$day1);

if($stmt2->execute())
{
    $stmt2->store_result();
    $stmt2->bind_result($slot2);

    $i=0;
    while($stmt2->fetch())
    {
        $slots0[$i++] = $slot2;
    }
    
    $i = 0;
    foreach($slots1 as $s1)
    {
        if(!in_array($s1,$slots0,TRUE))
        {
            $date1[$i++] = $s1;
        }
    }
    
    $result['today'] = $date1;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

//date1
$query2 = "SELECT slot FROM appointment where doc_id=? AND timestamp>? AND timestamp<?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("iii",$doc_id,$day1,$day2);

if($stmt2->execute())
{
    $stmt2->store_result();
    $stmt2->bind_result($slot2);

    $i=0;
    while($stmt2->fetch())
    {
        $slots2[$i++] = $slot2;
    }
    
    $i = 0;
    foreach($slots1 as $s1)
    {
        if(!in_array($s1,$slots2,TRUE))
        {
            $date2[$i++] = $s1;
        }
    }
    
    $result['date1'] = $date2;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

//date2
$query2 = "SELECT slot FROM appointment where doc_id=? AND timestamp>? AND timestamp<?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("iii",$doc_id,$day2,$day3);

if($stmt2->execute())
{
    $stmt2->store_result();
    $stmt2->bind_result($slot2);

    $i=0;
    while($stmt2->fetch())
    {
        $slots3[$i++] = $slot2;
    }
    
    $i = 0;
    foreach($slots1 as $s1)
    {
        if(!in_array($s1,$slots3,TRUE))
        {
            $date3[$i++] = $s1;
        }
    }
    
    $result['date2'] = $date3;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}


//date3
$query2 = "SELECT slot FROM appointment where doc_id=? AND timestamp>? AND timestamp<?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("iii",$doc_id,$day3,$day4);

if($stmt2->execute())
{
    $stmt2->store_result();
    $stmt2->bind_result($slot4);

    $i=0;
    while($stmt2->fetch())
    {
        $slots4[$i++] = $slot4;
    }
    
    $i = 0;
    foreach($slots1 as $s1)
    {
        if(!in_array($s1,$slots4,TRUE))
        {
            $date4[$i++] = $s1;
        }
    }
    
    $result['date3'] = $date4;
    http_response_code(200);
}
else{
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

$conn->close();

?>
