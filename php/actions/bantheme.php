<?php

//Marks a suggested theme as banned
function BanTheme($bannedTheme){
	global $themes, $dbConn, $ip, $userAgent;

	//Authorize user (logged in)
	$user = IsAdmin();
	if($user === false){
		AddAuthorizationWarning("Not logged in.", false);
		return;
	}

	//Authorize user (is admin)
	if(IsAdmin() === false){
		AddAuthorizationWarning("Only admins can delete themes.", false);
		return;
	}

	$bannedTheme = trim($bannedTheme);
	if($bannedTheme == ""){
		AddDataWarning("Theme is blank", false);
		return;
	}

	$clean_bannedTheme = mysqli_real_escape_string($dbConn, $bannedTheme);
	$clean_ip = mysqli_real_escape_string($dbConn, $ip);
	$clean_userAgent = mysqli_real_escape_string($dbConn, $userAgent);

	//Check that theme actually exists
	$sql = "SELECT theme_id FROM theme WHERE theme_banned != 1 AND theme_text = '$clean_bannedTheme'";
	$data = mysqli_query($dbConn, $sql);
	$sql = "";

	if(mysqli_num_rows($data) == 0){
		AddDataWarning("Theme does not exist", false);
		return;
	}

	$sql = "UPDATE theme SET theme_banned = 1 WHERE theme_banned != 1 AND theme_text = '$clean_bannedTheme'";
	$data = mysqli_query($dbConn, $sql);
	$sql = "";
    
    AddToAdminLog("THEME_BANNED", "Theme '$bannedTheme' banned", "");

	LoadThemes();

	AddDataSuccess("Theme Banned", false);
}

if(IsAdmin()){
    $bannedTheme = $_POST["theme"];
    BanTheme($bannedTheme);
}

?>