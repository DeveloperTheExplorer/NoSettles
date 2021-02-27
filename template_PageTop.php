<?php
include_once("php_includes/check_login_status.php");
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
?><?php 
$pageTop ='
<div id="pageTop1">
  <div id="pageTopWrap">
    <div><a class="pageTopLogo" href="/"></a>
    </div>
      <div id="up">
        <div class="right">
        <form name="loginform"  onSubmit="return false;">
	    <input type="email" class="textBox textBox1" name="Email" placeholder="Email" id="email" onfocus="emptyElement(\'status2\')" maxlength="88">
	    </div>
      </div>
      <div id="middle">
        <div class="right">
		<input type="password" class="textBox textbox2" placeholder="Password" id="password" onfocus="emptyElement(\'status2\')" maxlength="100" />
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
<div id="invite" onclick="fadeOut(\'invite\',\'invite_pop\')"></div>
<div id="invite_pop" style="padding: 5px;"><h2 id="invite_h2" style="border-bottom: 2px solid #C2BCBC;"></h2><div id="invite_div"></div><p id="invite_p"></p></div>';

?>
<script>
function restrict(elem){
	var tf = _(elem);
	var rx = new RegExp;
	if(elem == "email"){
		rx = /[' "]/gi;
	} else if(elem == "username"){
		rx = /[^a-z0-9]/gi;
	}
	tf.value = tf.value.replace(rx, "");
}
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
		var ajax = ajaxObj("POST", "template_PageTop.php");
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

<?php echo $pageTop;?>
<?php include_once("template_nav.php"); ?>
