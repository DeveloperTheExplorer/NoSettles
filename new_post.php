<?php
include_once("php_includes/check_login_status.php");
session_start();
// If user is logged in, header them away
if(isset($log_username)){
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
// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$country = $row["country"];
	$userlevel = $row["userlevel"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	$access_token = $row["access_token"];
	$user_id = $row["user_id"];
	$refresh_token = $row["refresh_token"];
	$publish_key = $row["publish_key"];
	if($gender == "f"){
		$sex = "Female";
	}
	
}
?><?php 
define('CLIENT_ID', 'ca_6VWCTqAZS0aX6kZYNUY67zqT2fPRqhIs');
define('API_KEY', 'sk_live_ptrhuLYkYkGZFqYFBdDTHxQJ');
define('TOKEN_URI', 'https://connect.stripe.com/oauth/token');
define('AUTHORIZE_URI', 'https://connect.stripe.com/oauth/authorize');
if ($access_token == "" || $refresh_token == "" || $publish_key == "" || $user_id == "") {
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
    $access_token = $resp['access_token'];
	$user_id = $resp['stripe_user_id'];
	$publishable_key = $resp['stripe_publishable_key'];
	$refresh_token = $resp['refresh_token'];
	$sql = "UPDATE users SET access_token='$access_token', refresh_token='$refresh_token', publish_key='$publishable_key', user_id='$user_id' WHERE username='$log_username' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
	header('location: https://nosettles.com/new_post.php');
	exit();
		
  } else if (isset($_GET['error'])) { // Error
    echo $_GET['error_description'];
  
  } else {
	header('location: https://nosettles.com/login_st.php');
	exit();  
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>NoSettles</title>
  <link rel="stylesheet" href="style/style.css">
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
    #pageTop {
		margin-top: -128px;
	}
  </style>
  <script src="js/main.js"></script>
  <script src="js/Ajax.js"></script>
  <script src="js/trSlide.js"></script>
</head>
<body style="background-color: #eee; font-family: arial, Verdana, Geneva, sans-serif;">

<?php include_once("template_UserPageTop.php"); ?>


<div id="pageMiddle" style="margin-top: 110px; background-color: #eee; height: 750px;">
	<?php include_once("template_status.php"); ?>
</div>

<div id="pageBottom">NoSettles Copyright &copy; 2015</div>
</body>
</html>