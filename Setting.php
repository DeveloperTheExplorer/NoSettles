<?php
include_once("php_includes/check_login_status.php");
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
} else {
	header("location: http://www.nosettles.com/");
	exit();
}
?><?php
include_once("php_includes/check_login_status.php");
// Initialize any variables that the page might echo
$u = "";
$sex = "Male";
$userlevel = "";
$avatar = "";
$country = "";
$joindate = "";
$lastsession = "";
// Make sure the _GET username is set, and sanitize it
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
}
// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
// Now make sure that user exists in the table
$numrows = mysqli_num_rows($user_query);
if($user_query < 1){
	echo "That user does not exist or is not yet activated, press back";
    exit();	
}
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
}
// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$country = $row["country"];
	$userlevel = $row["userlevel"];
	$avatar = $row["avatar"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	if($gender == "f"){
		$sex = "Female";
	}
}
?><?php

$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$log_user_query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($user_query);
while ($row = mysqli_fetch_array($log_user_query, MYSQLI_ASSOC)) {
	$log_avatar = $row["avatar"];
}
$SPP = "";
$pp2 = "";
$pp3 = "";
	
if ($avatar == "") {
	$SPP = "<div class='upload'>";
	$SPP .= "<form id='image_SP' enctype='multipart/form-data' method='post'>";
	$SPP .= '<input accept="image/*" type="file" name="FileUpload" id="fu_SP" onchange="doUpload(\'fu_SP\')"/>';
	$SPP .= "</form>";
	$SPP .= "</div>";
	$SPP .= '<img src="style/DPP.png" id="SDP" alt="Profile Pic" onclick="triggerUpload(event, \'fu_SP\')" />';
} else {
	$SPP = "<div class='upload'>";
	$SPP .= "<form id='image_SP' enctype='multipart/form-data' method='post'>";
	$SPP .= '<input accept="image/*" type="file" name="FileUpload" id="fu_SP" onchange="doUpload(\'fu_SP\')"/>';
	$SPP .= "</form>";
	$SPP .= "</div>";
	$SPP .= "<img src='Profile_P/$log_avatar' id='SDP' alt='Profile Pic' onclick='triggerUpload(event, \"fu_SP\")' />";
}
?><?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["emailcheck"])){
	include_once("php_includes/db_conx.php");
	$email = preg_replace('#[^a-z0-9]#i', '', $_POST['emailcheck']);
	$sql = "SELECT id FROM users WHERE email='$email' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $e_check = mysqli_num_rows($query);
    if (strlen($email) < 12) {
	    echo '<strong style="color:#F00;">Please enter a valid email.</strong>';
	    exit();
    }
	if (is_numeric($email[0])) {
	    echo '<strong style="color:#F00;">Email is not valid.</strong>';
	    exit();
    }
    if ($e_check < 1) {
	    echo '<strong style="color:#09F;">Your email is ok.</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $email . ' is taken</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["e"]) || isset($_POST["p"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if ($e_check > 0){ 
        echo "That email address is already in use in the system.";
        exit();
	} else if (strlen($e) < 10 && strlen($e) > 0) {
        echo "Email is not valid.";
        exit(); 
    } else if (is_numeric($e[0])) {
        echo 'Email is not valid.';
        exit();
    } else if ($e == "") {
		$p_hash = md5($p);
		// Add user info into the database table for the main site table
		$sql = "UPDATE users SET password='$p_hash' WHERE username='$log_username' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		echo "update_success";
		exit();
	} else if ($p == "") {
		$sql = "UPDATE users SET email='$e' WHERE username='$log_username' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		echo "update_success";
		exit();
	} else {
		$p_hash = md5($p);
		// Add user info into the database table for the main site table
		$sql = "UPDATE users SET email='$e', password='$p_hash' WHERE username='$log_username' LIMIT 1";
		$query = mysqli_query($db_conx, $sql); 
		echo "update_success";
		exit();
	}
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>NoSettles</title>
  <meta name="viewport" content="initial-scale=1.0,width=device-width" />
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageTop1.css" />
  <link rel="stylesheet" href="style/pageTop3.css" media="only screen and (max-width: 700px)" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageMiddle1.css" />
  <link rel="stylesheet" media="only screen and (max-width: 1100px)" href="style/pageMiddle2.css" />
  <link rel="stylesheet" media="only screen and (max-width: 700px)" href="style/pageMiddle3.css" />
  <link rel="icon" href="style/tabicon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="style/tabicon.png" type="image/x-icon" />
  <script src="js/main.js"></script>
  <script src="js/Ajax.js"></script>
  <script src="js/trSlide.js"></script>
<script>
function doUpload(id){
	var file = _(id).files[0];
	if(file.name === ""){
		return false;		
	}
	if(file.type != "image/jpeg" && file.type != "image/gif" && file.type != "image/png"){
		alert("That file type is not supported.");
		return false;
	}
	_("profile_p").innerHTML = "<div id='outer1'></div>";
	var formdata = new FormData();
	formdata.append("PPic", file);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "php_parsers/photo_system.php");
	ajax.send(formdata);	
}  
function progressHandler(event) {
	var percent = (event.loaded / event.total) * 100;
	_("outer1").innerHTML = "<div id='inner'>"+percent+"%</div>";
	_("inner").style.width = percent+'%';
}
function completeHandler(event){
	var action = "";
	var data = event.target.responseText;
	var datArray = data.split("|");
	if(datArray[0] == "upload_complete"){
		hasImage = datArray[1];
		_("profile_p").innerHTML = '<img src="Profile_P/'+datArray[1]+'" id="SDP" />';
		action = "send";
		Profile('send');
	} else {
		_("profile_p").innerHTML = datArray[0];
		_("SDP").style.display = "block";
	}
}
function errorHandler(event){
	_("profile_p").innerHTML = "Upload Failed";
	_("SDP").style.display = "block";
}
function abortHandler(event){
	_("profile_p").innerHTML = "Upload Aborted";
	_("SDP").style.display = "block";
}
function triggerUpload(e,elem){
	e.preventDefault();
	_(elem).click();	
}
function Profile(action) {
	var ajax = ajaxObj("POST", "php_parsers/Profile_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
			if(datArray[0] == "yiss"){
				var PIC = datArray[1];
				_("profile_p").innerHTML = '<img src="Profile_P/'+PIC+'" id="SDP" />';
				hasImage = "";
			} else {
				alert(ajax.responseText);
				alert('Somting wong!');
			}
		}
	};
	ajax.send("action=send&image="+hasImage);
}
function checkpassword(Password1) {
	var password1 = _("Password").value;
	var password2 = _("Password1").value;
	if (password1 != password2) {
		_("stats").innerHTML = "Your passwords do not match.";
	} else if (password1.length < 6 || password1.length > 20) {
		_("stats").innerHTML = "Please choose a password between 6 to 20 characters.";
	}
}
function emailcheck(){
	var e = _("Email").value;
	if(e != ""){
		_("stats").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "Setting.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("stats").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("emailcheck="+e);
	}
}
function emptyElement(x){
	_(x).innerHTML = "";
}
function update(){
	var e = _("Email").value;
	var p1 = _("Password").value;
	var p2 = _("Password1").value;
	var status = _("status_register");
	if(p1 != p2){
		_("stats").innerHTML = "Your password fields do not match.";
	}  else if(p1.length < 6 && p1.length > 0) {
		_("stats").innerHTML = "Your password must have at least 6 characters.";
	} else {
		_("stats").innerHTML = "Please wait...";
		var ajax = ajaxObj("POST", "Setting.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText != "update_success"){
					_("stats").innerHTML = ajax.responseText;
				} else {
					window.scrollTo(0,0);
					_("stats").innerHTML = "<span style='color: #0F0;'> Your account have been updated.</span>";
				}
	        }
        }
	 if(e.length < 10) {
		ajax.send("p="+p1);
	} else {
        ajax.send("e="+e+"&p="+p1);
	}
	}
}

</script>
</head>
<body style="background-color: #eee;">

<?php include_once("template_UserPageTop.php"); ?>

<div id="pageMiddle" style="margin-top: 110px;background-color: #eee;padding: 20px;height: 725px;">
  <div id="settings">
      <form name="signupform" id="update" onsubmit="return false;">
        <h3 style="color: #18B7FF;">Your new Email:</h3>
        <input type="email" id="Email" class="newPost allRow stRow S_txt" onBlur="emailcheck()" onfocus="emptyElement('stats')" placeholder="Write your new email here..." />
        <h3 style="margin-top: 30px; color: #18B7FF;">Your new password:</h3>
        <input type="password" id="Password" class="newPost allRow stRow S_txt" placeholder="Write your new password here..." />
        <input type="password" id="Password1" class="newPost allRow stRow S_txt" onBlur="checkpassword()" onfocus="emptyElement('stats')" placeholder="Confirm your new password" />
        <button type="submit" style="float: left;width: 51%;height: 41px;margin-top: 10px;" id="st_button" onfocus="emptyElement('stats')" class="logIn regBttn allRow rdRow" onclick="update()">
	      Update my profile
	    </button>
        <span id="stats" style="color: #F00;"></span>
      </form>
        <div id="profile_p">
          <h3 style="color: #18B7FF;clear: both;display: block;">To change your profile picture simply click on the image.</h3>
          <?php echo $SPP; ?>
    </div> 
  </div>
</div>

<div id="pageBottom">NoSettles Copyright &copy; 2015</div>
</body>
</html>