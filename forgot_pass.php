<?php
include_once("php_includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: User.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// AJAX CALLS THIS CODE TO EXECUTE
if(isset($_POST["e"])){
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$sql = "SELECT id, username FROM users WHERE email='$e' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	if($numrows > 0){
		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
			$id = $row["id"];
			$u = $row["username"];
		}
		$emailcut = substr($e, 0, 4);
		$randNum = rand(10000,99999);
		$tempPass = "$emailcut$randNum";
		$hashTempPass = md5($tempPass);
		$sql = "UPDATE useroptions SET temp_pass='$hashTempPass' WHERE username='$u' LIMIT 1";
	    $query = mysqli_query($db_conx, $sql);
		$to = "$e";
		$from = "auto_responder@nosettles.com";
		$headers ="From: $from\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$subject ="yoursite Temporary Password";
		$msg = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>NoSettles</title><style>a{text-decoration: none;color: #3EA7FD;}a:hover{color: #0073c0;text-decoration: underline;}h2{display: inline-block;margin-left: 20px;margin-top: 10px;color: #fff;}body{background-color: #EFEFEF;}</style></head><body style="margin:0px; background-color: #eee; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#0073C0; font-size:24px; color:#CCC; height:60px;"><a href="http://www.nosettles.com"><img src="http://www.nosettles.com/style/logo.png" width="60" height="50" alt="NoSettles" style="border:none; float:left;"></a><h2>Hello '.$u.'</h2></div><p>This is an automated message from NoSettles. If you did NOT recently initiate the Forgot Password process, please disregard this email.</p><p>You indicated that you forgot your login password. We can generate a temporary password for you to log in with, then once logged in you can change your password to anything you like.</p><p>After you click the link below your password to login will be:<br /><b>'.$tempPass.'</b></p><p><a href="http://www.nosettles.com/forgot_pass.php?u='.$u.'&p='.$hashTempPass.'">Click here now to apply the temporary password shown above to your account</a></p><p>If you do not click the link in this email, no changes will be made to your account. In order to set your login password to the temporary password you must click the link above.</p></body></html>';
		if(mail($to,$subject,$msg,$headers)) {
			echo "success";
			exit();
		} else {
			echo "email_send_failed";
			exit();
		}
    } else {
        echo "no_exist";
    }
    exit();
}
?><?php
// EMAIL LINK CLICK CALLS THIS CODE TO EXECUTE
if(isset($_GET['u']) && isset($_GET['p'])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
	$temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
	if(strlen($temppasshash) < 10){
		exit();
	}
	$sql = "SELECT id FROM useroptions WHERE username='$u' AND temp_pass='$temppasshash' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	if($numrows == 0){
		header("location: message.php?msg=There is no match for that username with that temporary password in the system. We cannot proceed.");
    	exit();
	} else {
		$row = mysqli_fetch_row($query);
		$id = $row[0];
		$sql = "UPDATE users SET password='$temppasshash' WHERE id='$id' AND username='$u' LIMIT 1";
	    $query = mysqli_query($db_conx, $sql);
		$sql = "UPDATE useroptions SET temp_pass='' WHERE username='$u' LIMIT 1";
	    $query = mysqli_query($db_conx, $sql);
	    header("location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style/style.css">
<link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageTop1.css" />
  <link rel="stylesheet" href="style/pageTop3.css" media="only screen and (max-width: 700px)" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageMiddle1.css" />
  <link rel="stylesheet" media="only screen and (max-width: 1100px)" href="style/pageMiddle2.css" />
  <link rel="stylesheet" media="only screen and (max-width: 700px)" href="style/pageMiddle3.css" />
  <link rel="icon" href="style/tabicon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="style/tabicon.png" type="image/x-icon" />
  
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function forgotpass(){
	var e = _("email").value;
	if(e == ""){
		_("status").innerHTML = "Please type in your email address";
	} else {
		_("forgotpassbtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "forgot_pass.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
				var response = ajax.responseText;
				if(response == "success"){
					_("forgotpassform").innerHTML = '<h3>Step 2. Check your email inbox in a few minutes</h3><p>You can close this window or tab if you like.</p><br/> <a style=" color: #06B1FF;" href="/"> Click here to go back!</a>';
				} else if (response == "no_exist"){
					_("status").innerHTML = "Sorry that email address is not in our system";
				} else if(response == "email_send_failed"){
					_("status").innerHTML = "Mail function failed to execute";
				} else {
					_("status").innerHTML = "An unknown error occurred";
				}
	        }
        }
        ajax.send("e="+e);
	}
}
</script>
</head>
<body style="background-color: #eee;">
<?php include_once("template_PageTop.php"); ?>

<div id="pageMiddle">
 <div id="fp">
  <h3>Generate a temorary log in password</h3>
  <form id="forgotpassform" onsubmit="return false;">
    <div>Step 1: Enter Your Email Address</div>
    <input id="email" type="text" class="textBox textBox1 fptxt" placeholder="Email Adress" onfocus="_('status').innerHTML='';" maxlength="88">
    <br /><br />
    <button id="forgotpassbtn" class="logIn fpBttn" onclick="forgotpass()">Generate Temporary Log In Password</button> 
    <p id="status"></p>
  </form>
 </div>
</div>
<div id="pageBottom">NoSettles Copyright &copy; 2015</div>
</body>
</html>