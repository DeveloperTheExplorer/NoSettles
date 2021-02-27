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
  $sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
  $user_query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$access_token = $row["access_token"];
	$user_id = $row["user_id"];
	$refresh_token = $row["refresh_token"];
	$publish_key = $row["publish_key"];
  }
  if($access_token != "" && $user_id != "" && $user_id != "" && $publish_key != "") {
		header("location: https://www.nosettles.com/new_post.php");
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
$access_token = "";
?><?php
  define('CLIENT_ID', 'ca_6VWCTqAZS0aX6kZYNUY67zqT2fPRqhIs');
  define('API_KEY', 'sk_live_ptrhuLYkYkGZFqYFBdDTHxQJ');
  define('TOKEN_URI', 'https://connect.stripe.com/oauth/token');
  define('AUTHORIZE_URI', 'https://connect.stripe.com/oauth/authorize');
  if (isset($_GET['code'])) { // Redirect w/ code
    $code = $_GET['code'];
    $token_request_body = array(
      'client_secret' => API_KEY,
      'grant_type' => 'authorization_code',
      'client_id' => CLIENT_ID,
      'code' => $code,
    );
    $req = curl_init(TOKEN_URI);
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($req, CURLOPT_POST, true );
    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
    // TODO: Additional error handling
    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
    $resp = json_decode(curl_exec($req), true);
    curl_close($req);
    echo $resp['access_token'];
	
  } else if (isset($_GET['error'])) { // Error
    echo $_GET['error_description'];
  
  } else { // Show OAuth link
    $authorize_request_body = array(
      'response_type' => 'code',
      'scope' => 'read_write',
      'client_id' => CLIENT_ID
    );
    $url = AUTHORIZE_URI . '?' . http_build_query($authorize_request_body);
    $connect = "<a href='$url' class='stripe-connect'></a>";
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
  <div id="stripe">
    <h3 style="color: #09F;">For receiving donations please sign in to Stripe.</h3>
    <?php echo $connect; ?>
        <div id="stripe_info">
        <p style="margin-bottom: 0; text-align:center;"><b>NOTE</b>: If you do not own a business related to your fundraiser, fill in the business sections with these information.</p>
        <div id="stripe_ul">
          <p>For "about your business" section, write about your fundraiser.</p>
          <p>For "business type", choose "Individual/Sole Proprietorship".</p>
          <p>For "Business number", you do not have to write anything.</p>
          <p>For "business address", write the address of your home.</p>
          <p>For "your website", write "https://nosettles.com".</p>
          <p>For "Business name", write NoSettles.</p>
        </div>
    </div>
  </div>

</div>

<div id="pageBottom">NoSettles Copyright &copy; 2015</div>

</body>

</html>