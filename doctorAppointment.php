<?php
include 'Database.php';

$db = new Database();
$conn = $db->connect();

$hours24 = $db->hours;

///////////////////////////

function slotsForDate($doc_id,$startDay,$endDay,$conn,$db)
{
    $dateQuery = "SELECT slot FROM doc_slots WHERE doc_id=? AND slot NOT IN(SELECT slot FROM appointment WHERE doc_id=? AND timestamp BETWEEN ? AND ?)";
    $dateStmt = $conn->prepare($dateQuery);
    $dateStmt->bind_param("iiii",$doc_id,$doc_id,$startDay,$endDay);
    
    if($dateStmt->execute())
    {
        $dateStmt->store_result();
        $dateStmt->bind_result($slotDate);
    
        $i=0;
        while($dateStmt->fetch())
        {
            $slotsDate[$i++] = $slotDate;
        }
            
        $slotsDate = $db->sort_slots($slotsDate);
        
        http_response_code(200);
        return $slotsDate;
    }
    else
    {
        $error = true;
        $message = "failure";
        $result = null;
        http_response_code(200);
    }
}

///////////////////////

//post params
$doc_id = $_POST['doc_id'];

//json params
$error = false;
$message = "success";
$result = array();

date_default_timezone_set('Asia/Kolkata');

$hour24 = 86400;

$currentHour = (int) date('h');
$currentMinute = (int) date('i');
$currentSecond = (int) date('s');
$currentDay = (int) date('d');
$currentYear = (int) date('Y');
$currentMonth = (int) date('m');

$currentTime = time();

$day0 = mktime(0, 0, 0, $currentMonth, $currentDay,$currentYear);

$day1 = $day0 + $hour24;
$day2 = $day1 + $hour24;
$day3 = $day2 + $hour24;
$day4 = $day3 + $hour24;

$result['doc_id'] = $doc_id;

$docSlotsQuery = "SELECT slot FROM doc_slots WHERE doc_id=?";

//getting all slots
$docSlotsStmt = $conn->prepare($docSlotsQuery);
$docSlotsStmt->bind_param("i",$doc_id);

if($docSlotsStmt->execute())
{
    $docSlotsStmt->store_result();
    $docSlotsStmt->bind_result($docSlot);
    
    $i=0;

    while($docSlotsStmt->fetch())
    {
        $docSlots[$i++] = $docSlot;//storing in 24 hour format
    }
}

//today
$todayQuery = "SELECT slot FROM doc_slots WHERE doc_id=? AND slot NOT IN(SELECT slot FROM appointment WHERE doc_id=? AND timestamp BETWEEN ? AND ?)";

$todayStmt = $conn->prepare($todayQuery);
$todayStmt->bind_param("iiii",$doc_id,$doc_id,$currentTime,$day1);

if($todayStmt->execute())
{
    $todayStmt->store_result();
    $todayStmt->bind_result($slotToday);

    $i=0;
    while($todayStmt->fetch())
    {
        $makeTime = mkTime($hours24[$slotToday],0,0,$currentMonth,$currentDay,$currentYear);
        if($makeTime>$currentTime)
            $slotsToday[$i++] = $slotToday;
    }
    
    $slotsToday = $db->sort_slots($slotsToday);
    
    if($slotsToday==null)
        $slotsToday = array();
    
    $result['today'] = $slotsToday;
    http_response_code(200);
}
else{
    $error = true;
    $message = "failure";
    $result = null;
    http_response_code(200);
}


//date1
$result['date1'] = slotsForDate($doc_id,$day1,$day2,$conn,$db);

//date2
$result['date2'] = slotsForDate($doc_id,$day2,$day3,$conn,$db);

//date3
$result['date3'] = slotsForDate($doc_id,$day3,$day4,$conn,$db);

$data = array(
    "error" => $error,
    "message" => $message,
    "result" => $result
);

$json = json_encode($data);

echo $json;

$conn->close();

?>