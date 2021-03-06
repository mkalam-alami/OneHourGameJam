<?php

//Add a suggested theme
function AddTheme($newTheme, $isBot){
	global $themes, $dbConn, $ip, $userAgent;

	if($isBot){
		$user = "bot";
	}else{
		//Authorize user (logged in)
		$user = IsLoggedIn();
		if($user === false){
			AddAuthorizationWarning("Not logged in.", false);
			return;
		}
	}

	$newTheme = trim($newTheme);
	if($newTheme == ""){
		AddDataWarning("Theme is blank", false);
		return;
	}

	foreach($themes as $i => $theme){
		if(strtolower($theme["theme"]) == strtolower($newTheme)){
			//Theme is already suggested
			AddDataWarning("This theme has already been suggested.", false);
			return;
		}
	}

	$clean_ip = mysqli_real_escape_string($dbConn, $ip);
	$clean_userAgent = mysqli_real_escape_string($dbConn, $userAgent);
	$clean_newTheme = mysqli_real_escape_string($dbConn, $newTheme);
	$clean_userName = mysqli_real_escape_string($dbConn, $user["username"]);

	//Insert new theme
	$sql = "
		INSERT INTO theme
		(theme_datetime, theme_ip, theme_user_agent, theme_text, theme_author)
		VALUES (Now(), '$clean_ip', '$clean_userAgent', '$clean_newTheme', '$clean_userName');";
	$data = mysqli_query($dbConn, $sql);
	$sql = "";

	LoadThemes();

	AddDataSuccess("Theme added", false);
}

if(IsLoggedIn()){
    $newTheme = $_POST["theme"];
    AddTheme($newTheme, false);
}

?>