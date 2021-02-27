<?php
include_once("php_includes/check_login_status.php");
// If user is logged in, header them away
if($log_username == ""){
	header("location: http://www.nosettles.com/");
	exit();
} 
?><?php 
$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
  $access_token = $row["access"];
  $account_id = $row["account_id"];
}
if($access_token == "" && $account_id == "") {
	header("location: https://www.nosettles.com/login_wp.php");
	exit();
}
?><?php
    // WePay PHP SDK - http://git.io/mY7iQQ
    require 'wepay.php';

    // application settings
    $client_id = 119239;
    $client_secret = "cdf3ea984b";

    // change to useProduction for live environments
    Wepay::useStaging($client_id, $client_secret);

    $wepay = new WePay($access_token);

    // create the withdrawal
    $response = $wepay->request('account/get_update_uri', array(
        'account_id'    => $account_id,
        'mode'          => 'iframe'
    ));

    // display the response
	$uri = $response->uri;
	
	$withdraw = 'WePay.iframe_checkout("withdrawal_div", "'.$uri.'");';
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
function emptyElement(x){
	_(x).innerHTML = "";
}
</script>

</head>

<body style="background-color: #eee;">

<?php include_once("template_UserPageTop.php"); ?>

<div id="pageMiddle" style="margin-top: 110px;background-color: #eee;padding: 20px;height: 725px;">
  <div id="wepay">
    <h3 style="color: #09F;">For you to withdraw, you will need to verify yourself at here.</h3>
    <div id="withdrawal_div"><div>
	<script type="text/javascript" src="https://stage.wepay.com/min/js/iframe.wepay.js">
	</script>
    <script type="text/javascript">
	  <?php echo $withdraw; ?>
	</script>
				
    <p><b>NOTE</b>: You will do this only once. We will never get your credit credentials and it's all stored on WePay.</p>
    
      </div>
    </div>
  </div>
</div>

<div id="pageBottom" style="margin-top: 3%;">NoSettles Copyright &copy; 2015</div>

</body>


</html>