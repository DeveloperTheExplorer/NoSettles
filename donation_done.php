<?php 
if(isset($_GET['check_id']) && $_GET['check_id'] != "") {
  include_once("php_includes/check_login_status.php");
  $checkout_id = $_GET['check_id'];
  
  $sql = "SELECT * FROM Checkouts WHERE id='$checkout_id' LIMIT 1";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  $checknumrows = mysqli_num_rows($query);
  if ($checknumrows > 0) {
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		  $amount = $row['amount'];
		  $receiver = $row['receiver'];
		  $time = $row['Time'];
		  $donor = $row['donor'];
  }
  
  
  
  $sql = "SELECT * FROM users WHERE username='$receiver' LIMIT 1";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  $checknumrows = mysqli_num_rows($query);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	  $gender = $row['gender'];
	  if ($gender == 'm') {
		  $their = "his";
	  } elseif ($gender == 'f') {
		  $their = "her";
	  }
  }
  
  $amount1 = number_format($amount);
  
  
  } else {
	  header('location: https://nosettles.com/');
	  exit();
  }
  
  
} else {
	header('location: https://nosettles.com/');
	exit();
}
?><!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>NoSettles</title>
  <meta name="viewport" content="initial-scale=1.0,width=device-width" />
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageTop1.css" />
  <link rel="stylesheet" href="style/pageTop3.css" media="only screen and (max-width: 700px)" />
  <link rel="stylesheet" media="only screen and (max-width: 4000px)" href="style/pageMiddle1.css" />
  <link rel="stylesheet" media="only screen and (max-width: 1250px)" href="style/pageMiddle2.css" />
  <link rel="stylesheet" media="only screen and (max-width: 700px)" href="style/pageMiddle3.css" />
  <link rel="icon" href="style/tabicon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="style/tabicon.png" type="image/x-icon" />
  <style type="text/css">
#pageMiddle > h3{
	color: #999;  
}
#pageMiddle > h2{
	color: #0BB2FF;  
}
#pageMiddle > h1{
	color: #0BB2FF;  
}
#pageMiddle > h4{
	color: #999;  
}
#pageMiddle > p {
	color: #999;
}
</style>
  <script src="js/main.js"></script>
  <script src="js/Ajax.js"></script>
  <script src="js/autoScroll.js"></script>
  <script src="js/fade.js"></script>
  <script src="js/trBackground.js"></script>
  <script src="js/trDrop.js"></script>
  <script src="js/trSlide.js"></script>
</head>
<body style="background-color: #eee;">
<?php include_once("template_PageTop.php"); ?>

<div id="pageMiddle" style="margin: 0 auto;background-color: #fff;width: 60%;padding: 20px;margin-top: 20px;border-radius: 5px;border: 2px solid #ddd; text-align:center;">
<h1 style="color: #4ABC08;">Payment successful.</h1>
<h3>Successfuly paid $<?php echo $amount1; ?>(+ Service Fee) to <a href="https://nosettles.com/user.php?u=<?php echo $receiver; ?>"><?php echo $receiver; ?></a> at <?php echo $time; ?></h3>
<h4>You just helped <?php echo $receiver; ?> to get closer to <?php echo $their; ?> dreams!</h4>
<?php include_once("template_SignUp.php"); ?>
</div>
<div id="pageBottom" style="margin-top: 2%;">NoSettles Copyright &copy; 2015</div>
</body>
</html>