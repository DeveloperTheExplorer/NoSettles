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
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["e"])){
	// CONNECT TO THE DATABASE
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = md5($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($e == "" || $p == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		sleep(3);
		$sql = "SELECT id, username, password FROM users WHERE email='$e' AND activated='1' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
  
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Easy Fundraising Ideas</title>
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
.back-content > h1 {
    color: #00B3FF;
}
.back-content > h2 {
    color: #00B3FF;
}
.back-content > h3 {
    color: #00B3FF;
}
.header {
    text-align: center;
    color: #00B3FF;
    background-color: #fff;
    width: 85%;
    height: 100px;
    margin: 0 auto;
    border-bottom: 2px dashed #00B3FF;
    margin-bottom:0px;
    padding-bottom: 0px;
    padding: 5px
}
.back-content {
    background-color: #00B3FF;
    padding: 5px;
    margin-top: -18px;
    border-bottom: 3px solid #999;
	border: none;
}
#fundraising_sec {
    background-color: #fff;
    width: 65%;
    margin: 0 auto;
    text-align: center;
    color: #555;
}
#compare {
    text-align: center;
    width: 100%;
    height: 250px;
    display: block;
}
.compare-img1 {
    height: 200px;
    width: 300px;
    padding: 5px;
    float:left;
    transition: all 0.5s ease;
}
.compare-img1:hover {
    zoom: 1.1;
    transform: rotate(-7deg)
}
.compare-img2 {
    height: 200px;
    width: 300px;
    padding: 5px;
    float:right;
    transition: all 0.5s ease;
}
.compare-img2:hover {
    zoom: 1.1;
    transform: rotate(7deg)
}
#compare-p {
    display: inline-block;
    color: #00B3FF;
    font-size: 35px;
    font-weight: bold;
    text-align: center;
    margin: 0 auto;
    margin-top: 80px;
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
function emptyElement(x){
	_(x).innerHTML = "";
}

function login(){
	var e = _("email").value;
	var p = _("password").value;
	if(e == "" || p == ""){
		_("status2").innerHTML = "Please fill out all of the form data";
	} else {
		_("loginbtn").style.display = "none";
		_("loading").style.display = 'inline-block';
		var one = Math.floor((Math.random() * 20) + 1);
		var two = Math.floor((Math.random() * 10) + 1);
		fadeIn('invite','invite_pop');
		_('invite_p').innerHTML = "";
		_("invite_h2").innerHTML = 'Question';
		_("invite_div").innerHTML = '<h3 style="color: #999">Please answer the question below.</h3><input type="text" style="border: 1px solid #00C7FF;height: 29px;width: 242px;margin: 5px;border-radius: 15px;padding-left: 8px;" class="textBox textBox1" name="answer" placeholder="What\'s '+one+' plus '+two+'?" id="robot" /><button type="submit" style="height: 29px;border-radius: 18px;" class="logIn" id="loginbtn" onclick="answer('+one+', '+two+')">Answer</button>';
		
	}
}
function answer(one, two) {
	var total = one + two;
	if (_("robot").value != total) {
		fadeOut('invite','invite_pop');				
		_("loginbtn").style.display = "inline-block";
		_("loading").style.display = 'none';
		_("status2").innerHTML = "Please prove you're not a robot.";
		return false;
	} else {
		var ajax = ajaxObj("POST", "index.php");
		var e = _("email").value;
		var p = _("password").value;
		
		fadeOut('invite','invite_pop');
		_("loginbtn").style.display = "none";
		_("loading").style.display = 'inline-block';
		ajax.onreadystatechange = function() {
			if(ajaxReturn(ajax) == true) {
				if(ajax.responseText == "login_failed"){								
					_("loginbtn").style.display = "inline-block";
					_("loading").style.display = 'none';
					_("status2").innerHTML = "Login unsuccessful, please try again.";
				} else if (ajax.responseText == "robot") {								
					_("loginbtn").style.display = "inline-block";
					_("loading").style.display = 'none';
					_("status2").innerHTML = "Please prove you're not a robot.";
				} else {
					window.location = "https://nosettles.com/user.php?u="+ajax.responseText;
				}
			}
		}
		ajax.send("e="+e+"&p="+p);
	}
}
function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
</script>
</head>
<body style="background-color: #eee;">
<div id="pageTop1">
  <div id="pageTopWrap">
    <div><a class="pageTopLogo" href="/"></a>
    </div>
      <div id="up">
        <div class="right">
        <form name="loginform"  onSubmit="return false;">
	    <input type="email" class="textBox textBox1" name="Email" placeholder="Email" id="email" onfocus="emptyElement('status2')" maxlength="88">
	    </div>
      </div>
      <div id="middle">
        <div class="right">
		<input type="password" class="textBox textbox2" placeholder="Password" id="password" onfocus="emptyElement('status2')" maxlength="100" />
        <button type="submit" class="logIn" id="loginbtn" onclick="login()">
              Log in
        </button><img id="loading" src="style/ajax-loader1.gif" class="load" style="height: 27px; width: 47px;" alt="Loading..." />
       </form>
      </div>
        </div>
          <div id="down">          
            <div class="right">
            <a class="links link1" href="forgot_pass.php">Forgot Password?</a>
            <p id="status2" style="color: #f00"></p>
            </div>
          </div>
        </div>
  </div>
</div>
<?php include_once("template_nav.php"); ?>

<div id="java_pop_up"></div>
<div id="pop_up"><p>Secure Checkout</p><div id="wepay_checkout"></div></div>
<div id="invite" onclick="fadeOut('invite','invite_pop')"></div>
<div id="invite_pop" style="padding: 5px;"><h2 id="invite_h2" style="border-bottom: 2px solid #C2BCBC;"></h2><div id="invite_div"></div><p id="invite_p"></p></div>
<script type="text/javascript" src="https://stage.wepay.com/min/js/iframe.wepay.js">
</script>

<div id="pageMiddle" style="margin-top: 0px; padding-top: 25px;">
<div id="fundraising_sec" class="back-content">
  <h1 class="header">Easy Fundraising Ideas</h1>
    <h2>Are you searching for Easy Fundraising Ideas?</h2>
    <p>Have you been searching for easy fundraising ideas for hours and you're now frustrated on how to make a fundraiser. Are you looking at fundraisers and trying to figure out how they got their funds? Are you looking for money but don't know how to start a fundraiser that can get popular and get funded.</p>
    <h2>Well, you have found what you are looking for.</h2>
    <p>If the answer to the questions above were mostly yes, then you have found the right place. The place where you can find easy fundraising ideas. The place where you can generate funds easily and with the lowest fees possible.</p>
    <h3>Introducing NoSettles</h3>
    <p>NoSettles is a fund-free website where you can post whatever you want and receive donations for that. You will not need to pay fees that take your money for no reason. Here you are able to sign up for free under 5 minutes and get donations as quickly as possible.</p>
    <h2>Start your own fundraiser without having to overthink it.</h2>
    <p>If you want to start your own fundraiser but you are looking around in Google, looking for websites where you can find easy fundraising ideas, then you are wasting your time. Instead you could start your fundraiser right away without having to research for hours.</p>
    <h3>Don't wait any longer, no need for searching for Easy Fundraising Ideas.</h3>
    <p>Just make your own fundraiser. You deserve better. If you want something but you can't afford it, then make a fundraiser. You don't have to settle for cheap products that don't last a month. You don't have to go with low-cost products. You deserve more!</p>
    <h2>Judge for yourself</h2>
    <div id="compare">
        <img src="images/good-laptop.jpg" alt="Easy Fundraising Ideas" class="compare-img1" />
        <p id="compare-p">OR</p>
        <img src="images/bad-laptop.jpg" alit="Easy Fundraising Ideas" class="compare-img2" />
    </div>
    <div id="compare">
        <img src="images/good-car.jpg" alt="Easy Fundraising Ideas" class="compare-img1" />
        <p id="compare-p">OR</p>
        <img src="images/bad-car.jpg" alit="Easy Fundraising Ideas" class="compare-img2" />
    </div>
    <div id="compare">
        <img src="images/good-plane.jpg" alt="Easy Fundraising Ideas" class="compare-img1" />
        <p id="compare-p">OR</p>
        <img src="images/bad-plane.jpg" alit="Easy Fundraising Ideas" class="compare-img2" />
    </div>
    <?php include_once("template_SignUp.php"); ?>
  </div>
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
	_('invite_p').innerHTML = 'Invitation Code is a string that every None-Settler has. If you know any friends that is a member, you can ask them for the code. Otherwise, you can <a href="mailto:contact@nosettles.com">contact us</a>,tell us why you want to join and how you found us, then simply ask for the Invitation Code.';
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