<?php
$PP = "";
$pp2 = "";
$pp3 = "";
include_once("php_includes/check_login_status.php");

if (isset($_POST['action']) && $_POST['action'] == "read_notif"){
  mysqli_query($db_conx, "UPDATE notifications SET did_read='1' WHERE username='$log_username' AND did_read='0' LIMIT 20") or die(mysqli_error($db_conx));
  echo "notif_ok";
}

$sql = "SELECT * FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
$log_user_query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
$numrows = mysqli_num_rows($log_user_query);
while ($row = mysqli_fetch_array($log_user_query, MYSQLI_ASSOC)) {
	$log_avatar = $row["avatar"];
	$log_email = $row["email"];
	$invite_code = $row['code'];
}
if ($log_avatar == "") {
	$pp2 = '<img src="style/DPP.png" id="DDP2" alt="Profile Pic" />';
} else {
	$pp2 = "<img src='Profile_P/$log_avatar' id='DDP2' alt='Profile Pic' />";
}

$notif = "";
$notif1 = "";
$sql = "SELECT * FROM notifications WHERE username='$log_username' AND did_read='0'";
$log_user_query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
$numrows = mysqli_num_rows($log_user_query);
if($numrows > 0) {
	if ($numrows > 99) {
		$numrows = "+99";
	}
	$notif = "<span id='notif'>".$numrows."</span>";
	$notif1 = "<span id='notif1'>".$numrows."</span>";
} else {
	$notif = "<span id='notif' style='display: none;'>".$numrows."</span>";
	$notif1 = "<span id='notif1' style='display: none;'>".$numrows."</span>";
}

$sql = "SELECT * FROM notifications WHERE username='$log_username' ORDER BY date_time DESC LIMIT 20";
$log_user_query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
$numrows = mysqli_num_rows($log_user_query);
if($numrows > 0) {
while ($row = mysqli_fetch_array($log_user_query, MYSQLI_ASSOC)) {
	$note = $row["note"];
	$read = $row["did_read"];
	$time = $row["date_time"];
	include_once("classes/develop_php_library.php");
	$timeAgoObject = new convertToAgo;
	$convertedTime = ($timeAgoObject -> convert_datetime($time)); // Convert Date Time
	$when = ($timeAgoObject -> makeAgo($convertedTime));
	if ($read == 0) {
	  $content .= "<div class='notifs' style='color: #666; background-color: #fff; font-weight: bold;'>".$note."<p style='margin: 0;'>".$when."</p></div>";
	} else {
	  $content .= "<div class='notifs' style='color: #666; background-color: #fff;'>".$note."<p style='margin: 0;'>".$when."</p></div>";
	}
}
} else {
	$content = "<p style='color: #999; text-align: center; font-size: 24px;'>You do not have any notifications.</p>";
}

$sql2 = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$log_user_query2 = mysqli_query($db_conx, $sql2) or die(mysqli_error($db_conx));
$numrows2 = mysqli_num_rows($log_user_query2);
while ($row2 = mysqli_fetch_array($log_user_query2, MYSQLI_ASSOC)) {
	$avatar = $row2["avatar"];
}
if ($log_username == $u){
	if ($avatar == "") {
		$PP = "<div class='upload'>";
		$PP .= "<form id='image_SP' enctype='multipart/form-data' method='post'>";
		$PP .= '<input accept="image/*" type="file" name="FileUpload" id="fu_SP" onchange="doUpload(\'fu_SP\')"/>';
		$PP .= "</form>";
		$PP .= "</div>";
		$PP .= '<img src="style/DPP.png" id="DDP" alt="Profile Pic" onclick="triggerUpload(event, \'fu_SP\')" />';
	} else {
		$PP = "<div class='upload'>";
		$PP .= "<form id='image_SP' enctype='multipart/form-data' method='post'>";
		$PP .= '<input accept="image/*" type="file" name="FileUpload" id="fu_SP" onchange="doUpload(\'fu_SP\')"/>';
		$PP .= "</form>";
		$PP .= "</div>";
		$PP .= "<img src='Profile_P/".$avatar."' id='DDP' alt='Profile Pic' onclick='triggerUpload(event, \"fu_SP\")' />";
	}
} else {
    if ($avatar == "") {
		$PP = '<img src="style/DPP.png" id="DDP" alt="Profile Pic" />';
    } else {
		$PP = "<img src='Profile_P/".$avatar."' id='DDP' alt='Profile Pic'>";
	}	
}
?><?php 
$pageTop ='
<div id="invite" onclick="fadeOut(\'invite\',\'invite_pop\')"></div>
<div id="invite_pop" style="padding: 5px;"><h2 id="invite_h2" style="border-bottom: 2px solid #C2BCBC;"></h2><div id="invite_div"></div><p id="invite_p"></p><div id="invite_code">'.$invite_code.'</div></div>
<div id="pageTop">
  <div id="pageTopTools">
    <div id="menu">
      
      <!-- Menu icon -->
      <div class="icon-close" onClick="slideOut(\'menu\');">
        <img src="style/close.png" alt="Close" style="padding: 10px;" />
		
      </div>
      <!-- Menu -->
      <ul>
        <a href="user.php?u='.$log_username.'" id="P_link">'.$pp2.''.$log_username.'</a>
		<a href="#" id="notiff" class="M_links" onclick="notif()"><li style="border-top: 2px solid #FFF;">Notifications '.$notif.'<span class="icon6"></span></li></a>
		<a href="tips.php" class="M_links"><li>Tips<span class="icon7"></span></li></a>
        <a href="received.php" class="M_links"><li>Donations received <span class="icon8"></span></li></a>
		<a href="donated.php?u='.$log_username.'" class="M_links"><li>Amount donated <span class="icon8"></span></li></a>
        <a href="Feed.php" class="M_links"><li>Feed <span class="icon1"></span></li></a>
        <a href="#" class="M_links" onclick="fadeIn(\'invite\',\'invite_pop\'); code()"><li>Invite<span class="icon2"></span></li></a>
        <a href="Setting.php" class="M_links"><li>Settings <span class="icon3"></span></li></a>
        <a href="fees.php" class="M_links"><li>Fees <span class="icon4"></span></li></a>
        <a href="mailto:contact@nosettles.com" class="M_links"><li>Contact <span class="icon5"></span></li></a>
      </ul>
    </div>
     <div class="icon-menu" onClick="slideIn(\'menu\');">
	 '.$notif1.'
      </div>
     </div>
  <div id="notification"><div class="arrow-up"></div><div id="notif-back">Notifications<div id="notif-content">'.$content.'</div></div></div>
  
  <div id="pageTopWrap2">
    <div><a class="pageTopLogo" href="Feed.php"></a>
    </div>
    <div id="pageTopRest2">
    <div id="left">
    <a class="new_post" href="new_post.php">+</a>
    </div>
	<!-- Seach -->
	  <div id="memSearch">
        <input id="searchUsername" type="text" autocomplete="off" onKeyUp="getNames(this.value)" placeholder="Search for None-Settlers" >
        <div id="memSearchResults"></div>
	  </div>
	<!-- Seach -->
    <div id="right">
      <form action="/logout.php">
    	<button class="logIn logout" type="submit">Log out</button>
	  </form>
      </div>
    </div>
  </div>
</div>';

?>
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
function code() {
	_('invite_code').style.visibility= "visible";
	_('invite_div').innerHTML = "";
	_('invite_h2').innerHTML = "Invite Code";
	_('invite_p').innerHTML = '<p>Give this code to your friends to invite them.</p>Invitation Code: ';	
	_('invite_p').style.display = 'inline';
}
function notif() {
  _("notification").style.display = "block"; 
  _("notif").style.display = "none";
  _("notif1").style.display = "none";
  var action = "read_notif";
  var ajax = ajaxObj("POST", "template_UserPageTop.php");
  ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "notif_ok"){
			} else {
				
			}
		}
	};
  ajax.send("action="+action);	
}
window.addEventListener('mouseup', function(event){
	var box = document.getElementById('notification');
	var box1 = document.getElementById('notif-back');
	var box2 = document.getElementById('notif-content');
	if (event.target != box && event.target.parentNode != box && event.target != box1 && event.target.parentNode != box1 && event.target != box2 && event.target.parentNode != box2){
        box.style.display = 'none';
    }
});
</script>


<?php
echo $pageTop;
?>
