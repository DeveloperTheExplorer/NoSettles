<?php
include_once("php_includes/check_login_status.php");
include_once("classes/develop_php_library.php");
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("php_includes/db_conx.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#09F;">' . $username . ' is OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	$c = preg_replace('#[^a-z ]#i', '', $_POST['c']);
	$i = $_POST['i'];
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	//--------------------------------------------
	$sql = "SELECT id FROM users WHERE code='$i' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$i_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $e == "" || $p == "" || $g == "" || $c == "" || $i == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		$p_hash = md5($p);
		$hash_u = md5($u);
		$code = md5($hash_u);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO users (username, email, password, gender, country, ip, signup, lastlogin, notescheck, code, invited_code)       
		        VALUES('$u','$e','$p_hash','$g','$c','$ip',now(),now(),now(),'$code','$i')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_conx, $sql);
		// Email the user their activation link
		$to = "$e";							 
		$from = "auto_responder@nosettles.com";
		$subject = 'NoSettles Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>NoSettles</title><style>a{text-decoration: none;color: #3EA7FD;}a:hover{color: #0073c0;text-decoration: underline;}h5{display: inline-block;margin-left: 20px;margin-top: 10px;color: #fff;}body{background-color: #EFEFEF;}</style></head><body style="margin:0px; background-color: #eee; font-family:Tahoma, Geneva, sans-serif;"> <div style="padding:10px; background:#0073C0; font-size:24px; color:#CCC;"><a href="https://www.nosettles.com"><img src="https://www.nosettles.com/style/logo.png" width="60" height="50" alt="NoSettles" style="border:none; float:left;"></a> <h5>NoSettles Activation</h5></div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="https://www.nosettles.com/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash.'">Click here to activate your account now</a><br /><br />After successful activation you can login using your::<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		$headers = "From: $from\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
		mail($to, $subject, $message, $headers);
		
		$sql = "SELECT username FROM users WHERE code='$i' LIMIT 1";
    	$query = mysqli_query($db_conx, $sql);
		while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			$inviter = $row['username'];
		}
		if ($g == 'm') {
		  $their = "him";
	    } elseif ($g == 'f') {
		  $their = "her";
	    }
		$avatar_notif = '<img src="style/DPP.png" class="CPP" alt="Profile Pic" />';
		$app = "Invite";
  		$note = '<a href="user.php?u='.$u.'" style="color: #09F;"><img src="style/invited.png" alt="Donation" class="icon_donation" />'.$avatar_notif.'<img src="style/invited.png" alt="Donation" class="icon_donation flip" /><li class="Notification">Your friend '.$u.' just joined NoSettles. Check '.$their.' out!</li></a>';
		mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) 
		             VALUES('$inviter','$u','$app','$note',now())") or die(mysqli_error($db_conx));
		
		echo "signup_success";
		exit();
	}
}
?><?php
$statuslist = "";
?><?php $sql = "SELECT * FROM status WHERE type='a' ORDER BY postdate DESC LIMIT 20";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
if($statusnumrows > 0){
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$statusid = $row["id"];
	$account_name = $row["account_name"];
	$author = $row["author"];
	$postdate = $row["postdate"];
	$timeAgoObject = new convertToAgo;
	$convertedTime = ($timeAgoObject -> convert_datetime($postdate)); // Convert Date Time
	$when = ($timeAgoObject -> makeAgo($convertedTime));
	$amount = $row["amount"];
	$amount = nl2br($amount);
	$amount = str_replace("&amp;","&",$amount);
	$amount= stripslashes($amount);
	$received = $row["received"];
	$received = nl2br($received);
	$received = str_replace("&amp;","&",$received);
	$received= stripslashes($received);
	$title = $row["title"];
	$title = nl2br($title);
	$title = str_replace("&amp;","&",$title);
	$title = stripslashes($title);
	$data = $row["data"];
	$data = nl2br($data);
	$data = str_replace("&amp;","&",$data);
	$data = stripslashes($data);
	$image = $row["image"];
	$image = nl2br($image);
	$image = str_replace("&amp;","&",$image);
	$image = stripslashes($image);
	$statusDeleteButton = '';
	if($author == $log_username || $account_name == $log_username ){
		$statusDeleteButton = '<span id="sdb_'.$statusid.'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\');" title="DELETE THIS STATUS AND ITS REPLIES">  delete post</a></span> &nbsp; &nbsp;';
	}
	if ($image == '<img src="permUploads/na" class="status_img" />') {
	  $image = '<img src="style/blue-camera-icon.png" class="status_img">';
	}
	$sql3 = "SELECT * FROM users WHERE username='$author' LIMIT 1";
	$query3 = mysqli_query($db_conx, $sql3);
	$statusnumrows3 = mysqli_num_rows($query3);
	while ($row3 = mysqli_fetch_array($query3, MYSQLI_ASSOC)) {
		$avatar = $row3["avatar"];
	}
	///////////////////////////////////////////////////
	// GATHER UP ANY STATUS REPLIES
	$status_replies = "";
	$query_replies = mysqli_query($db_conx, "SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate DESC");
	$replynumrows = mysqli_num_rows($query_replies);
    if($replynumrows > 0){
        while ($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)) {
			$statusreplyid = $row2["id"];
			$replyauthor = $row2["author"];
			$replydata = $row2["data"];
			$replydata = nl2br($replydata);
			$replypostdate = $row2["postdate"];
			$timeAgoObject = new convertToAgo;
			$convertedTime = ($timeAgoObject -> convert_datetime($replypostdate)); // Convert Date Time
			$when1 = ($timeAgoObject -> makeAgo($convertedTime));
			$replydata = str_replace("&amp;","&",$replydata);
			$replydata = stripslashes($replydata);
			$replyDeleteButton = '';
			if($replyauthor == $log_username || $account_name == $log_username ){
				$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">remove</a></span>';
			}
			//avatars for replies...
			$sql2 = "SELECT * FROM users WHERE username='$replyauthor' AND activated='1' LIMIT 1";
			$log_user_query2 = mysqli_query($db_conx, $sql2);
			$numrows2 = mysqli_num_rows($log_user_query2);
			while ($row2 = mysqli_fetch_array($log_user_query2, MYSQLI_ASSOC)) {
				$avatar5 = $row2["avatar"];
			}
			if ($avatar5 == "") {
				$CPP = '<img src="style/DPP.png" class="CPP" alt="Profile Pic" />';
			} else {
				$CPP = "<img src='Profile_P/".$avatar5."' class='CPP' alt='Profile Pic'>";
			}
			$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes">'.$CPP.'<div id="comment"><b>Reply by <a href="user.php?u='.		$replyauthor.'" target="_blank">'.$replyauthor.'</a> '.$when1.':</b> '.$replyDeleteButton.'<br />'.$replydata.'</div></div>';
        }
    } else {
		$status_replies = "<div id='no_c'> No comments found, be the first one!</div>";
	}
	//avatars for posts...
	$sql3 = "SELECT * FROM users WHERE username='$author' AND activated='1' LIMIT 1";
	$log_user_query3 = mysqli_query($db_conx, $sql3);
	$numrows3 = mysqli_num_rows($log_user_query3);
	while ($row3 = mysqli_fetch_array($log_user_query3, MYSQLI_ASSOC)) {
		$avatar8 = $row3["avatar"];
	}
	if ($avatar8 == "") {
		$DDP2 = '<img src="style/DPP.png" id="DDP2" alt="Profile Pic" />';
	} else {
		$DDP2 = "<img src='Profile_P/".$avatar8."' id='DDP2' alt='Profile Pic'>";
	}
		

	$total = $received / $amount;
	$percent = $total * 100;
	$percentage = round($percent, 0, PHP_ROUND_HALF_UP);
	if ($percent > 100) {
		$percent = 100;
	} else if ($received == 0) {
		$percentage = 0;
		$percent = 0;
	}
	$amount1 = number_format($amount);
	
	
	$statuslist .= '<div id="status_'.$statusid.'"><div id="status"><div id="status_post"><a href="user.php?u='.$author.'" style="font-size: 24px;font-weight: bold;color: #0DB3FF;">'.$DDP2.''.$author.'</a><div class="top_center">'.$title.'</div><div class="status_date"> '.$when.''.$statusDeleteButton.'</div><div class="top_right"><img src="style/ajax-loader.gif" id="load_'.$statusid.'" class="load" alt="Loading..." /><button type="button" class="donate_ntn" id="button_'.$statusid.'" onclick="donate(\'amount_'.$statusid.'\',\''.$author.'\',\''.$title.'\',\''.$statusid.'\')">Donate</button><input type="number" name="donation" placeholder="Amount" class="dnt_amount" id="amount_'.$statusid.'"/><div class="amount"><p style="margin: 0;font-weight: normal;">$'.$amount1.'</p><div class="status_outer"><div class="status_inner" style="width: '.$percent.'%;"></div></div><p style="margin: 0;font-weight: normal;">'.$percentage.'% Done</p></div></div><br /><div class="RHD"><div class="center_left">'.$data.'</div></div><div class="center_right">'.$image.'</div><div class="RHD" style="text-align: center; margin-top: 5px;"><div id="comments">'.$status_replies.'</div></div><div id="st_ok"></div></div>';
	    
		$statuslist .= '<textarea id="status_comment" class="replytext status_comment" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button id="st_button" onclick="replyToStatus()">Reply</button></div></div>';	
}
} else {
	$statuslist = '<div id="No_post">No posts found.</div>';
}

$one = rand(1, 10);
$two = rand(5, 15);
$total = $one + $two;
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NoSettles</title>
  <meta name="description" content="Sign up. Make a post for the things you want. Tell your friends about it. Receive donations.">
  <meta name="viewport" content="initial-scale=1.0,width=device-width" />
  <meta name="msvalidate.01" content="25A9DC1385D08C4DF90A1DCE8F58723A" />
  <meta http-equiv="content-language" content="en-gb">
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageTop1.css" />
  <link rel="stylesheet" href="style/pageTop3.css" media="only screen and (max-width: 700px)" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageMiddle1.css" />
  <link rel="stylesheet" media="only screen and (max-width: 1250px)" href="style/pageMiddle2.css" />
  <link rel="stylesheet" media="only screen and (max-width: 700px)" href="style/pageMiddle3.css" />
  <link rel="icon" href="style/tabicon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="style/tabicon.png" type="image/x-icon" />
  <style type="text/css">
#signupform{
	margin-top:24px;	
}
#signupform > div {
	margin-top: 12px;	
}
#signupform > input,select {
	width: 200px;
	padding: 3px;
	background: #F3F9DD;
}
#signupbtn {}
#terms {
	border:#CCC 1px solid;
	background: #F5F5F5;
	padding: 12px;
}
</style>
  <script src="js/main.js"></script>
  <script src="js/Ajax.js"></script>
  <script src="js/autoScroll.js"></script>
  <script src="js/fade.js"></script>
  <script src="js/trBackground.js"></script>
  <script src="js/trDrop.js"></script>
  <script src="js/trSlide.js"></script>
  <script>
function replyToStatus() {
	_("st_ok").innerHTML = "Please login or sign up to comment, post and donate.";
}

function checkusername(){
	var u = _("u").value;
	if(u != "" ){
		if (u == "anonymous" || u == "Anonymous") {
			_("unamestatus").innerHTML = "Please choose another name.";
			return false;
		}
		_("unamestatus").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "index.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("unamestatus").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("usernamecheck="+u);
	}
}
function signup(){
	var u = _("u").value;
	var e = _("e").value;
	var p1 = _("p1").value;
	var p2 = _("p2").value;
	var c = _("c").value;
	var g = _("g").value;
	var i = _("i").value;
	var status = _("status_register");
	if(u == '' || e == '' || p1 == '' || p2 == '' || c == '' || g == '') {
		_("status_register").innerHTML = "Please fill in all of the required fields.";
	} else if(p1 != p2){
		_("status_register").innerHTML = "Your password fields do not match.";
	} else if(e.length < 10) {
		_("status_register").innerHTML = "Please enter a valid email adress.";
	} else if(p1.length < 6) {
		_("status_register").innerHTML = "Your password must have at least 6 characters.";
	} else {
		var ajax = ajaxObj("POST", "index.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "signup_success"){
					status.style.color = "#3399FF";
					status.innerHTML = "Congrats! Check your email for activation.";					
				} else {
					status.innerHTML = ajax.responseText;

				}
	        }
        }
        ajax.send("u="+u+"&e="+e+"&p="+p1+"&c="+c+"&g="+g+"&i="+i);
	}
}
var loading;
var button;
var width = screen.width;
var height = screen.height;
if (width < 950) {
	alert("Sorry, our website is not compatible with phones yet! Please use a computer instead.");
	window.location = "https://facebook.com/";
}
function donate(id, owner, title, statusid) {
	var conf = confirm("If you donate without signing up, your name will be anonymous.");
	if (conf !== true) {
		return false;
	}
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
	var ajax = ajaxObj("POST", "index.php");
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
	_("loginbtn").style.display = "inline-block";
	_("loading").style.display = 'none';
	if (loading != "" && button != "") {
	  _(loading).style.display = "none";
	  _(button).style.display = "block"
	}
};

/* function addEvents(){
	_("elemID").addEventListener("click", func, false);
}
window.onload = addEvents; */
</script>

</head>
<body>
<?php include_once("template_PageTop.php"); ?>

<div id="java_pop_up"></div>
<div id="pop_up"><p>Secure Checkout</p><div id="wepay_checkout"></div></div>

<script type="text/javascript" src="https://stage.wepay.com/min/js/iframe.wepay.js">
</script>

<div id="pageMiddle" style="margin-top: 0px;">
<div id="back-regis">
<form name="signupform" id="regis" onsubmit="return false;">
<h2>It's completely free.</h2>
<!--Top row or first row -->
<span id="unamestatus"></span>
<input type="text" id="u" class="textBox textBox1 allRow stRow" placeholder="Username" onblur="checkusername()" onkeyup="restrict('username')" maxlength="16"  />

<input id="e" type="email" class="textBox textBox1 allRow stRow" placeholder="Email" onblur="checkusername()" onfocus="emptyElement('status_register')" onkeyup="restrict('email')" maxlength="88" />


<select id="g" class="ndRow allRow" onfocus="emptyElement('status_register')">
      <option value="">Gender</option>
      <option value="m">Male</option>
      <option value="f">Female</option>
</select>

<input type="password" id="p1" class="textBox textBox1 allRow ndRow" placeholder="Password" onfocus="emptyElement('status_register')" maxlength="100" />

<input type="password" id="p2" class="textBox textBox1 allRow ndRow" placeholder="Re-enter Password" onfocus="emptyElement('status_register')" maxlength="100" />

<select id="c" class="allRow rdRow" onfocus="emptyElement('status_register')">
      <option value="">Country...</option>
<option value="Afganistan">Afghanistan</option>
<option value="Albania">Albania</option>
<option value="Algeria">Algeria</option>
<option value="American Samoa">American Samoa</option>
<option value="Andorra">Andorra</option>
<option value="Angola">Angola</option>
<option value="Anguilla">Anguilla</option>
<option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
<option value="Argentina">Argentina</option>
<option value="Armenia">Armenia</option>
<option value="Aruba">Aruba</option>
<option value="Australia">Australia</option>
<option value="Austria">Austria</option>
<option value="Azerbaijan">Azerbaijan</option>
<option value="Bahamas">Bahamas</option>
<option value="Bahrain">Bahrain</option>
<option value="Bangladesh">Bangladesh</option>
<option value="Barbados">Barbados</option>
<option value="Belarus">Belarus</option>
<option value="Belgium">Belgium</option>
<option value="Belize">Belize</option>
<option value="Benin">Benin</option>
<option value="Bermuda">Bermuda</option>
<option value="Bhutan">Bhutan</option>
<option value="Bolivia">Bolivia</option>
<option value="Bonaire">Bonaire</option>
<option value="Bosnia &amp; Herzegovina">Bosnia &amp; Herzegovina</option>
<option value="Botswana">Botswana</option>
<option value="Brazil">Brazil</option>
<option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
<option value="Brunei">Brunei</option>
<option value="Bulgaria">Bulgaria</option>
<option value="Burkina Faso">Burkina Faso</option>
<option value="Burundi">Burundi</option>
<option value="Cambodia">Cambodia</option>
<option value="Cameroon">Cameroon</option>
<option value="Canada">Canada</option>
<option value="Canary Islands">Canary Islands</option>
<option value="Cape Verde">Cape Verde</option>
<option value="Cayman Islands">Cayman Islands</option>
<option value="Central African Republic">Central African Republic</option>
<option value="Chad">Chad</option>
<option value="Channel Islands">Channel Islands</option>
<option value="Chile">Chile</option>
<option value="China">China</option>
<option value="Christmas Island">Christmas Island</option>
<option value="Cocos Island">Cocos Island</option>
<option value="Colombia">Colombia</option>
<option value="Comoros">Comoros</option>
<option value="Congo">Congo</option>
<option value="Cook Islands">Cook Islands</option>
<option value="Costa Rica">Costa Rica</option>
<option value="Cote DIvoire">Cote D'Ivoire</option>
<option value="Croatia">Croatia</option>
<option value="Cuba">Cuba</option>
<option value="Curaco">Curacao</option>
<option value="Cyprus">Cyprus</option>
<option value="Czech Republic">Czech Republic</option>
<option value="Denmark">Denmark</option>
<option value="Djibouti">Djibouti</option>
<option value="Dominica">Dominica</option>
<option value="Dominican Republic">Dominican Republic</option>
<option value="East Timor">East Timor</option>
<option value="Ecuador">Ecuador</option>
<option value="Egypt">Egypt</option>
<option value="El Salvador">El Salvador</option>
<option value="Equatorial Guinea">Equatorial Guinea</option>
<option value="Eritrea">Eritrea</option>
<option value="Estonia">Estonia</option>
<option value="Ethiopia">Ethiopia</option>
<option value="Falkland Islands">Falkland Islands</option>
<option value="Faroe Islands">Faroe Islands</option>
<option value="Fiji">Fiji</option>
<option value="Finland">Finland</option>
<option value="France">France</option>
<option value="French Guiana">French Guiana</option>
<option value="French Polynesia">French Polynesia</option>
<option value="French Southern Ter">French Southern Ter</option>
<option value="Gabon">Gabon</option>
<option value="Gambia">Gambia</option>
<option value="Georgia">Georgia</option>
<option value="Germany">Germany</option>
<option value="Ghana">Ghana</option>
<option value="Gibraltar">Gibraltar</option>
<option value="Great Britain">Great Britain</option>
<option value="Greece">Greece</option>
<option value="Greenland">Greenland</option>
<option value="Grenada">Grenada</option>
<option value="Guadeloupe">Guadeloupe</option>
<option value="Guam">Guam</option>
<option value="Guatemala">Guatemala</option>
<option value="Guinea">Guinea</option>
<option value="Guyana">Guyana</option>
<option value="Haiti">Haiti</option>
<option value="Hawaii">Hawaii</option>
<option value="Honduras">Honduras</option>
<option value="Hong Kong">Hong Kong</option>
<option value="Hungary">Hungary</option>
<option value="Iceland">Iceland</option>
<option value="India">India</option>
<option value="Indonesia">Indonesia</option>
<option value="Iran">Iran</option>
<option value="Iraq">Iraq</option>
<option value="Ireland">Ireland</option>
<option value="Isle of Man">Isle of Man</option>
<option value="Israel">Israel</option>
<option value="Italy">Italy</option>
<option value="Jamaica">Jamaica</option>
<option value="Japan">Japan</option>
<option value="Jordan">Jordan</option>
<option value="Kazakhstan">Kazakhstan</option>
<option value="Kenya">Kenya</option>
<option value="Kiribati">Kiribati</option>
<option value="Korea North">Korea North</option>
<option value="Korea Sout">Korea South</option>
<option value="Kuwait">Kuwait</option>
<option value="Kyrgyzstan">Kyrgyzstan</option>
<option value="Laos">Laos</option>
<option value="Latvia">Latvia</option>
<option value="Lebanon">Lebanon</option>
<option value="Lesotho">Lesotho</option>
<option value="Liberia">Liberia</option>
<option value="Libya">Libya</option>
<option value="Liechtenstein">Liechtenstein</option>
<option value="Lithuania">Lithuania</option>
<option value="Luxembourg">Luxembourg</option>
<option value="Macau">Macau</option>
<option value="Macedonia">Macedonia</option>
<option value="Madagascar">Madagascar</option>
<option value="Malaysia">Malaysia</option>
<option value="Malawi">Malawi</option>
<option value="Maldives">Maldives</option>
<option value="Mali">Mali</option>
<option value="Malta">Malta</option>
<option value="Marshall Islands">Marshall Islands</option>
<option value="Martinique">Martinique</option>
<option value="Mauritania">Mauritania</option>
<option value="Mauritius">Mauritius</option>
<option value="Mayotte">Mayotte</option>
<option value="Mexico">Mexico</option>
<option value="Midway Islands">Midway Islands</option>
<option value="Moldova">Moldova</option>
<option value="Monaco">Monaco</option>
<option value="Mongolia">Mongolia</option>
<option value="Montserrat">Montserrat</option>
<option value="Morocco">Morocco</option>
<option value="Mozambique">Mozambique</option>
<option value="Myanmar">Myanmar</option>
<option value="Nambia">Nambia</option>
<option value="Nauru">Nauru</option>
<option value="Nepal">Nepal</option>
<option value="Netherland Antilles">Netherland Antilles</option>
<option value="Netherlands">Netherlands (Holland, Europe)</option>
<option value="Nevis">Nevis</option>
<option value="New Caledonia">New Caledonia</option>
<option value="New Zealand">New Zealand</option>
<option value="Nicaragua">Nicaragua</option>
<option value="Niger">Niger</option>
<option value="Nigeria">Nigeria</option>
<option value="Niue">Niue</option>
<option value="Norfolk Island">Norfolk Island</option>
<option value="Norway">Norway</option>
<option value="Oman">Oman</option>
<option value="Pakistan">Pakistan</option>
<option value="Palau Island">Palau Island</option>
<option value="Palestine">Palestine</option>
<option value="Panama">Panama</option>
<option value="Papua New Guinea">Papua New Guinea</option>
<option value="Paraguay">Paraguay</option>
<option value="Peru">Peru</option>
<option value="Phillipines">Philippines</option>
<option value="Pitcairn Island">Pitcairn Island</option>
<option value="Poland">Poland</option>
<option value="Portugal">Portugal</option>
<option value="Puerto Rico">Puerto Rico</option>
<option value="Qatar">Qatar</option>
<option value="Republic of Montenegro">Republic of Montenegro</option>
<option value="Republic of Serbia">Republic of Serbia</option>
<option value="Reunion">Reunion</option>
<option value="Romania">Romania</option>
<option value="Russia">Russia</option>
<option value="Rwanda">Rwanda</option>
<option value="St Barthelemy">St Barthelemy</option>
<option value="St Eustatius">St Eustatius</option>
<option value="St Helena">St Helena</option>
<option value="St Kitts-Nevis">St Kitts-Nevis</option>
<option value="St Lucia">St Lucia</option>
<option value="St Maarten">St Maarten</option>
<option value="St Pierre &amp; Miquelon">St Pierre &amp; Miquelon</option>
<option value="St Vincent &amp; Grenadines">St Vincent &amp; Grenadines</option>
<option value="Saipan">Saipan</option>
<option value="Samoa">Samoa</option>
<option value="Samoa American">Samoa American</option>
<option value="San Marino">San Marino</option>
<option value="Sao Tome &amp; Principe">Sao Tome &amp; Principe</option>
<option value="Saudi Arabia">Saudi Arabia</option>
<option value="Senegal">Senegal</option>
<option value="Serbia">Serbia</option>
<option value="Seychelles">Seychelles</option>
<option value="Sierra Leone">Sierra Leone</option>
<option value="Singapore">Singapore</option>
<option value="Slovakia">Slovakia</option>
<option value="Slovenia">Slovenia</option>
<option value="Solomon Islands">Solomon Islands</option>
<option value="Somalia">Somalia</option>
<option value="South Africa">South Africa</option>
<option value="Spain">Spain</option>
<option value="Sri Lanka">Sri Lanka</option>
<option value="Sudan">Sudan</option>
<option value="Suriname">Suriname</option>
<option value="Swaziland">Swaziland</option>
<option value="Sweden">Sweden</option>
<option value="Switzerland">Switzerland</option>
<option value="Syria">Syria</option>
<option value="Tahiti">Tahiti</option>
<option value="Taiwan">Taiwan</option>
<option value="Tajikistan">Tajikistan</option>
<option value="Tanzania">Tanzania</option>
<option value="Thailand">Thailand</option>
<option value="Togo">Togo</option>
<option value="Tokelau">Tokelau</option>
<option value="Tonga">Tonga</option>
<option value="Trinidad &amp; Tobago">Trinidad &amp; Tobago</option>
<option value="Tunisia">Tunisia</option>
<option value="Turkey">Turkey</option>
<option value="Turkmenistan">Turkmenistan</option>
<option value="Turks &amp; Caicos Is">Turks &amp; Caicos Is</option>
<option value="Tuvalu">Tuvalu</option>
<option value="Uganda">Uganda</option>
<option value="Ukraine">Ukraine</option>
<option value="United Arab Erimates">United Arab Emirates</option>
<option value="United Kingdom">United Kingdom</option>
<option value="United States of America">United States of America</option>
<option value="Uraguay">Uruguay</option>
<option value="Uzbekistan">Uzbekistan</option>
<option value="Vanuatu">Vanuatu</option>
<option value="Vatican City State">Vatican City State</option>
<option value="Venezuela">Venezuela</option>
<option value="Vietnam">Vietnam</option>
<option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
<option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
<option value="Wake Island">Wake Island</option>
<option value="Wallis &amp; Futana Is">Wallis &amp; Futana Is</option>
<option value="Yemen">Yemen</option>
<option value="Zaire">Zaire</option>
<option value="Zambia">Zambia</option>
<option value="Zimbabwe">Zimbabwe</option>
</select>
</select>

<button type="submit" id="signupbtn" style="margin-left: 1.45%;" class="logIn regBttn allRow rdRow" onclick="signup()">
Done!
</button>
<span id="status_register"></span>
<input id="i" type="text" class="textBox textBox1 allRow stRow" style="float:left;" placeholder="Invitation Code(optional)" onfocus="emptyElement('status_register')" maxlength="120"><div id="help" onclick="fadeIn('invite','invite_pop'); hint()">?</div>
<p>By clicking done, you agree to our <a href="terms-of-service.php">Terms of use</a> and our <a href="privacy-policy.php">Privacy Policy</a></p>

</form>
</div><br>

<!--<div id="slang">
  <div id="slang1">DON'T WAIT</div>
  <div id="slang2">DON'T SETTLE</div>
</div>-->

<div class="back-content">
<div class="content">
<div class="text1">
<h2>Get the things you want!</h2>
<p>Sign up. Make a post for what you want. Tell your friends. Receive donations.</p>
<a href="About.php">Learn more</a>
</div>
<div class="text2">
<h2>Your safety is our top priority!</h2>
<p>Safety is one of the most important things for a website, and that is why we are very proud to only use and allow Stripe.</p>
<a href="https://stripe.com/about" target="_blank">Stripe</a>
</div>
</div>
</div>
<?php echo $statuslist; ?>
</div>

<script>
function fadeIn(el, elen){
	var element = document.getElementById(elen);
	var elem = document.getElementById(el);
	element.style.transition = "all 0.5s linear 0s";
	elem.style.transition = "all 0.5s linear 0s";
	element.style.opacity = 1;
	elem.style.opacity = 1;
	element.style.visibility = "visible";
	elem.style.visibility = "visible";
	var body=document.getElementsByTagName('body')[0];
	body.style.overflow = "hidden";
}
function hint() {
	_('invite_div').innerHTML = "";
	_('invite_h2').innerHTML = "Invitation Code";
	_('invite_p').innerHTML = 'Has someone intoduced you to this website? If yes, then ask them about the code, if no, then simply leave the box blank.';
}
function fadeOut(el, elen){
	var element = document.getElementById(elen);
	var elem = document.getElementById(el);
	element.style.opacity = 0;
	elem.style.opacity = 0;
	element.style.visibility = "hidden";
	elem.style.visibility = "hidden";
	var body=document.getElementsByTagName('body')[0];
	body.style.overflowY = "scroll";	
}
  function mostRecent() {
	  var ajax = ajaxObj("POST", "index.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "login_failed"){
					_("status2").innerHTML = "Login unsuccessful, please try again.";
				}
			}
		}
     ajax.send("action=mostRecent");
  }
</script>
<div id="pageBottom" style="margin-top: 20px;"><div style="margin-top: -20px;">NoSettles Copyright &copy; 2015</div>
  <div style="padding-top: 5px;">
    <span id="siteseal">
	  <script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=6EFg6BS5bspEZsmRQHWfXAsaePte8smuuUzp4HtCti6trGWAiijB5qh7GRLG">
      </script>
    </span>
  </div>
</div>
</body>
</html>