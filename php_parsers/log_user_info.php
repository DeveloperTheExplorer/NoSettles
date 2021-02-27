<?php
include_once("../php_includes/check_login_status.php");
$log_avatar = '';
$log_profile_id = '';
$log_gender = '';
$log_country = '';
$log_userlevel = '';
$log_avatar = '';
$log_signup = '';
$log_lastlogin = '';
$log_joindate = '';
$log_lastsession = '';
$log_sex = 'Male'
?><?php 
$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$log_user_query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($user_query);
while ($row = mysqli_fetch_array($log_user_query, MYSQLI_ASSOC)) {
	$log_avatar = $row["avatar"];
	$log_profile_id = $row["id"];
	$log_gender = $row["gender"];
	$log_country = $row["country"];
	$log_userlevel = $row["userlevel"];
	$log_avatar = $row["avatar"];
	$log_signup = $row["signup"];
	$log_lastlogin = $row["lastlogin"];
	$log_joindate = strftime("%b %d, %Y", strtotime($log_signup));
	$log_lastsession = strftime("%b %d, %Y", strtotime($log_lastlogin));
	if($log_gender == "f"){
		$log_sex = "Female";
	}
}
?>