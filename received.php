<?php
include_once("php_includes/check_login_status.php");

?><?php 
if ($log_username == "") {
	header('location: https://nosettles.com/');
}
include_once("classes/develop_php_library.php");
$status = "";

$sql = "SELECT * FROM Checkouts WHERE receiver='$log_username' ORDER BY Time DESC";
$query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
$statusnumrows = mysqli_num_rows($query);
if($statusnumrows > 0){
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$donor = $row['donor'];
		$amount1 = $row['amount'];
		$time = $row['Time'];
		$timeAgoObject = new convertToAgo;
		$convertedTime = ($timeAgoObject -> convert_datetime($time)); // Convert Date Time
		$when = ($timeAgoObject -> makeAgo($convertedTime));
		$amount = number_format($amount1);
		
		$sql1 = "SELECT avatar FROM users WHERE username='$donor' AND activated='1' LIMIT 1";
		$log_user_query1 = mysqli_query($db_conx, $sql1) or die(mysqli_error($db_conx));
		while ($row1 = mysqli_fetch_array($log_user_query1, MYSQLI_ASSOC)) {
			$avatar = $row1["avatar"];	
		} 
		$DPP = "<img src='Profile_P/".$avatar."' id='DDP2' alt='Profile Pic'>";
		$user = "<a href='user.php?u=".$donor."'>".$donor."</a>";
			
		if ($donor == "" || $donor == "Anonymous" || $donor == "anonymous") {
			$DPP = '<img src="style/DPP.png" id="DDP2" alt="Profile Pic" />';
			$user = 'Anonymous';	
		} elseif ($avatar == "" && $donor != "" && $donor != "Anonymous" && $donor != "anonymous") {
			$DPP = '<img src="style/DPP.png" id="DDP2" alt="Profile Pic" />';
			$user = "<a href='user.php?u=".$donor."'>".$donor."</a>";
		}
		
		$status .= '<div class="rec_outer"><div class="rec_content"><div class="rec_left">'.$DPP.$user.'</div><div class="rec_right">Donated '.$when.'</div><div class="rec_center">Donated $'.$amount.'</div></div></div>';
	}
} else {
  $status = "<div style='text-align: center;color: #999;'><p style='font-size: 65px;margin: 0;'>Looks like you have not received any donations yet.</p><p style='margin: 0;'>For tips on getting donations, <a href='tips.php'>Click here</a>.</p></div>";

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
</head>
<body style="background-color: #eee;">

<?php include_once("template_UserPageTop.php"); ?>


<div id="pageMiddle" style="margin-top: 110px; background-color: #eee; padding: 20px;">
  
  <?php echo $status; ?>
  
</div>

<div id="pageBottom">NoSettles Copyright &copy; 2015</div>
</body>
</html>