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
} else {
    header("location: https://www.nosettles.com");
    exit();	
}
// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
// Now make sure that user exists in the table
$numrows = mysqli_num_rows($user_query);
if($numrows < 1){
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
	$donated = $row["donated"];
	$amount = number_format($donated);
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	if($gender == "f"){
		$sex = "Female";
	}
}
?><?php 
  if (isset($_POST['action']) && $_POST['action'] == "checkout") {
	$publish_key = "";
	$owner = $_POST['owner'];
	$donation = $_POST['donation'];
	$title = $_POST['title'];
	$statusid = $_POST['statusid'];
	$price = $donation * 100;
	if ($owner === $log_username) {
		echo "Unfortunately you cannot donate to yourself!";
		exit();
	}

	$sql = "INSERT INTO donation (receiver, title, price, status_id)       
		  VALUES('$owner','$title','$price','$statusid')";
    $query = mysqli_query($db_conx, $sql);
	$uid = mysqli_insert_id($db_conx);
	
	echo "success|".$uid."|";
  }
?><?php 
	if ($u === $log_username) {
		$sql = "SELECT id FROM status WHERE author='$log_username' LIMIT 1";
    	$query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
		$numrows = mysqli_num_rows($query);
		if ($numrows < 1) {
			$header_msg = "Make Your First Fundraiser!";
			$full_msg = "It looks like you have not made any fundraisers yet. No worries, we'll get you set up one under 5mins. <a id='msg_link' href='new_post.php'>Click here to begin</a>";
			$pop_msg = '<div id="msg" onclick="fadeOut(\'msg\',\'msg_pop\')"></div><div id="msg_pop" style="padding: 5px;"><h2 id="invite_h2" style="border-bottom: 2px solid #C2BCBC;">'.$header_msg.'</h2><div id="invite_div"></div><p id="invite_p">'.$full_msg.'</p></div>';
		} else {
			$pop_msg = "";
		}
	} else {
		$pop_msg = "";
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
  <style type="text/css"> 
#msg {
    visibility:visible;
    opacity:1;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.42);
    position: fixed;
    z-index: 100;
}
#msg_pop {
    visibility:visible;
    opacity:1;
	position: fixed;
    z-index: 3000;
    background-color: #E6E6E6;
    width: 500px;
    height: 300px;
    top: 10%;
    left: 50%;
    margin-left: -250px;
    text-align: center;
    color: #999;
    border-radius: 5px;
    box-shadow: 7px 7px 20px #444;
}
#msg_link {
    display: block;
    margin-top: 82px;
    width: 260px;
    margin-left: 118px;
    left: -130px;
    padding: 5px;
    font-size: 29px;
    border-radius: 10px;
    color: #fff;
    background-color: #09F;
    background-image: linear-gradient(rgba(0,0,0,0),rgba(0, 0, 0, 0.3));
    border: 1px solid #3079ed;
	transition: all 0.3s ease;
}
#msg_link:hover {
    background-color: #00BEFF;
}
  </style>
  <script src="js/main.js"></script>
  <script src="js/Ajax.js"></script>
  <script src="js/trSlide.js"></script>
  <script src="js/fade.js"></script>
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
		_("profile_p").innerHTML = '<img src="Profile_P/'+datArray[1]+'" id="DDP" />';
		action = "send";
		Profile('send');
	} else {
		_("profile_p").innerHTML = datArray[0];
		_("DDP").style.display = "block";
	}
}
function errorHandler(event){
	_("profile_p").innerHTML = "Upload Failed";
	_("DDP").style.display = "block";
}
function abortHandler(event){
	_("profile_p").innerHTML = "Upload Aborted";
	_("DDP").style.display = "block";
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
				_("profile_p").innerHTML = '<img src="Profile_P/'+PIC+'" id="DDP" />';
				hasImage = "";
			} else {
				alert(ajax.responseText);
				alert('Somting wong!');
			}
		}
	};
	ajax.send("action=send&image="+hasImage);
}
function donate(id, owner, title, statusid) {
	var donation = _(id).value;
	var loading = "load_"+statusid;
	var button = "button_"+statusid;
	if (donation == "" || donation < 1) {
		_(id).style.border = '2px solid #FF0000';
		return false;
	} else {
		_(id).style.border = '2px solid #0092F4';
	}
	_(loading).style.display = "inline-block";
	_(button).style.display = "none";
	var ajax = ajaxObj("POST", "user.php?u="+owner);
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
		if(datArray[0] == "success") {
			var stripe = datArray[1];
			datArray[2] = "";
			window.location = "https://nosettles.com/checkout.php?id="+stripe;
		} else {
			var body=document.getElementsByTagName('body')[0];
			_("java_pop_up").innerHTML = '<div id="pop_up_back" onclick="close_pop_up(\''+loading+'\',\''+button+'\')"></div>';
			_("pop_up_back").style.display = "block";
			_("pop_up").style.display = "block";
			body.style.overflow = "hidden";
			_("pop_up").innerHTML = "<p style='color: #999; font-size: 30px; margin-top: 100px'>"+ajax.responseText+"</p>";
			}
		}
	};
	ajax.send("action=checkout&donation="+donation+"&owner="+owner+"&title="+title+"&statusid="+statusid);
	
};
function close_pop_up(loading, button) {
	var body=document.getElementsByTagName('body')[0];
	body.style.overflowY = "scroll";
	_("pop_up_back").style.display = "none";
	_("pop_up").style.display = "none";
	_(loading).style.display = "none";
	_(button).style.display = "block"
};
</script>
</head>
<body style="background-color: #eee;">

<?php include_once("template_UserPageTop.php"); ?>

<div id="java_pop_up"></div>
<div id="pop_up"></div>

<?php echo $pop_msg; ?>

<div id="pageMiddle" style="margin-top: 110px; background-color: #eee; padding: 20px;">
  <div id="profile">
    <div id="profile_p1">
      <?php echo $PP; ?>
    </div>
    <h3><?php echo $u; ?></h3>
    <div class="row1">
      <p>Gender: <?php echo $sex; ?></p>
      <p>Country: <?php echo $country; ?></p>
    </div>
    <div class="row2">
      <p>Join Date: <?php echo $joindate; ?></p>
      <p><a href="donated.php?u=<?php echo $u; ?>" style="color: #0CF;">Amount Donated: $<?php echo $amount; ?></a></p>
	</div>
  </div>
  <?php include_once("status.php"); ?>
  
</div>

<div id="pageBottom">NoSettles Copyright &copy; 2015</div>
</body>
</html>