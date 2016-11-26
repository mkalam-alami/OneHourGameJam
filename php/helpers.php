<?php

function StartsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function EndsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

//Numeric comparator for arrays based on property
function CmpArrayByPropertyPopularityNum($a, $b)
{
	if(isset($a["banned"]) && $a["banned"] == 1){
		return 1;
	}
	return CmpArrayByProperty($a, $b, "popularity_num");
}

//Numeric comparator for arrays based on property
function CmpArrayByProperty($a, $b, $property)
{
	return $a[$property] < $b[$property];
}

//Gets the list of json file locations, sorted in ascending order
//Capped at 1000 entries.
function GetSortedJamFileList(){
	$filesToParse = Array();
	for($i = 0; $i < 1000; $i++){
		if(file_exists("data/jams/jam_$i.json")){
			$filesToParse[] = "data/jams/jam_$i.json";
		}
	}
	krsort($filesToParse);
	return $filesToParse;
}

//Returns ordinal version of provided number, so 1 -> 1st; 3 -> 3rd, etc.
function ordinal($number) {
	$number = intval($number);
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function GetNextJamDateAndTime(){
	global $dictionary, $dbConn;
	
  	// Display an event until 4 hours after its start time
	$currentGmtTime = gmdate("Y-m-d H:i:s");
	$sql = "SELECT jam_start_datetime FROM jam WHERE jam_deleted = 0 AND jam_start_datetime > DATE_SUB('$currentGmtTime', INTERVAL 4 HOUR) ORDER BY jam_start_datetime LIMIT 1";

	$data = mysqli_query($dbConn, $sql);
	$info = mysqli_fetch_array($data);

	if ($info != null) {
		$saturday = strtotime( $info['jam_start_datetime']);
	}
	else {
		// Default to next saturday in case the jam is not created yet
		$saturday = strtotime("saturday +20 hours");
	}

	$dictionary["next_jam_suggested_date"] = date("Y-m-d", $saturday);
	$dictionary["next_jam_suggested_time"] = date("H:i", $saturday);
	$now = time();
	$interval = $saturday - $now;
	$dictionary["seconds_until_jam_suggested_time"] = $interval;
	return $saturday;
	return $saturday;
}

function GetCurrentJamNumberAndID(){
	global $dbConn;
	
	$sql = "
		SELECT jam_id, jam_jam_number FROM jam WHERE jam_jam_number = (SELECT MAX(jam_jam_number) FROM jam WHERE jam_deleted = 0) AND jam_deleted = 0
	";
	$data = mysqli_query($dbConn, $sql);
	$sql = "";
	
	if($info = mysqli_fetch_array($data)){
		return Array("NUMBER" => intval($info["jam_jam_number"]), "ID" => intval($info["jam_id"]));
	}else{
		return Array("NUMBER" => 0, "ID" => 0);
	}
}

?>