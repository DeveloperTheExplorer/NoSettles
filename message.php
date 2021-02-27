<?php
$message = "";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if($msg == "activation_failure"){
	$message = '<h2>Activation Error</h2> Sorry there seems to have been an issue activating your account at this time. We have already notified ourselves of this issue and we will contact you via email when we have identified the issue.';
} else if($msg == "activation_success"){
	$message = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>NoSettles</title>
<style>
a {text-decoration: none;
color: #3EA7FD;
}
a:hover{
color: #0073c0;
text-decoration: underline;
}
h5{
display: inline-block;
margin-left: 20px;
margin-top: 10px;
color: #fff;
}
body{
background-color: #EFEFEF;
}
</style>
</head>
<body style="margin:0px; background-color: #eee; font-family:Tahoma, Geneva, sans-serif;">
<div style="padding:10px; background:#0073C0; font-size:24px; color:#CCC;">
<a href="http://www.yoursitename.com">
<img src="http://www.nosettles.com/style/logo.png" width="60" height="50" alt="NoSettles" style="border:none; float:left;">
</a>
<h5>NoSettles Activation Complete!</h5>
</div>
<div style="padding:24px; font-size:17px;">Congratulations!<br />
<br />
Your account has now been activated, and you are free to use it right now if you would like to!<br />
<br />
<a href="http://www.nosettles.com">Click here to login to your account now</a><br />
<br />
You can use your E-mail Address to login from now on!
</div>
</body>
</html>';
} else {
	$message = $msg;
}
?>
<div><?php echo $message; ?></div>