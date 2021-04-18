<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hours24 = $db->hours;

$doc_id = $_POST['doc_id'];

$currentTime = time();

$currentHour = (int) date('h');
$currentMinute = (int) date('i');
$currentSecond = (int) date('s');
$currentDay = (int) date('d');
$currentYear = (int) date('Y');
$currentMonth = (int) date('m');

$day0 = mktime(0, 0, 0, $currentMonth, $currentDay,$currentYear);//same day morning

$day1 = $day0 + 86400;//next day

date_default_timezone_set('Asia/Kolkata');

//previous
$previousQuery = "SELECT doc_id,timestamp from appointment where doc_id=? and timestamp<? order by timestamp desc";

$dateStmt = $conn->prepare($previousQuery);
$dateStmt->bind_param("ii",$doc_id,$currentTime);

if($dateStmt->execute())
{
    $dateStmt->store_result();
    $dateStmt->bind_result($d0,$timestamp0);

    $i=0;
    while($dateStmt->fetch())
    {
        if($timestamp<$currentTime)
            $slotsDate0[$i++] = date('d-m-Y h:i a',$timestamp0);
    }
    
    $result['previous'] = $slotsDate0;
    $error = false;
$message = "success";
    http_response_code(200);
}
else
{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}

//upcoming
$upcomingQuery = "SELECT doc_id,timestamp from appointment where doc_id=? and timestamp>? order by timestamp";
$dateStmt = $conn->prepare($upcomingQuery);
    $dateStmt->bind_param("ii",$doc_id,$day1);
    
    if($dateStmt->execute())
    {
        $dateStmt->store_result();
        $dateStmt->bind_result($d1,$timestamp1);
    
        $i=0;
        while($dateStmt->fetch())
        {
            $slotsDate1[$i++] = date('d-m-Y h:i a',$timestamp1);
        }
  
        http_response_code(200);
        $error = false;
$message = "success";
        $result['upcoming'] = $slotsDate1;

    }
    else
    {
        $error = true;
        $message = "failure";
        $result = null;
        http_response_code(200);
    }


//today
$todayQuery = "SELECT timestamp,slot from appointment where doc_id=? and timestamp between ? and ?";
$todayStmt = $conn->prepare($todayQuery);
$todayStmt->bind_param("iii",$doc_id,$currentTime,$day1);

if($todayStmt->execute())
{
    $todayStmt->store_result();
    $todayStmt->bind_result($timestamp2,$slotToday);

    $i=0;
    while($todayStmt->fetch())
    {
        $makeTime = mkTime($hours24[$slotToday],0,0,$currentMonth,$currentDay,$currentYear);
        if($makeTime>$currentTime)
            $slotsToday[$i++] = date('d-m-Y h:i a',$timestamp2);
    }
    
    if($slotsToday==null)
        $slotsToday = array();
    
    $result['todayAppointments'] = $slotsToday;
    $error = false;
    $message = "success";
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