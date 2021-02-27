<?php 
if(isset($_GET['id']) && $_GET['id'] != "") {
  include_once("php_includes/check_login_status.php");
  $id = $_GET['id'];
  
  $sql = "SELECT * FROM donation WHERE id='$id' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $checknumrows = mysqli_num_rows($query);
  if ($checknumrows > 0) {
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		  $price = $row['price'];
		  $receiver = $row['receiver'];
		  $status_id = $row['status_id'];
		  $title = $row['title'];
		  $amount = $price / 100;
  }
  
  
  $sql = "SELECT publish_key FROM users WHERE username='$receiver' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $publish_key = $row['publish_key'];
  }
  $sql = "SELECT email FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $email = $row['email'];
  }
  
  $amount1 = number_format($amount);
  
  $stripe = '<form action="checkout_done.php?id='.$id.'" method="POST">
			  <script
				src="https://checkout.stripe.com/checkout.js" class="stripe-button"
				data-key="'.$publish_key.'"
				data-amount="'.$price.'"
				data-email="'.$email.'"
				data-name="NoSettles"
				data-label="Donate $'.$amount1.'"
				data-description="'.$title.'"
				data-image="style/Logo.png">
			  </script>
			</form>';
  
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
<div id="pageTop1">
  <div id="pageTopWrap">
    <div><a class="pageTopLogo" href="/"></a>
    </div>
  </div>
</div>


<div id="pageMiddle" style="margin: 0 auto;background-color: #fff;width: 60%;padding: 20px;margin-top: 20px;border-radius: 5px;border: 2px solid #ddd; text-align:center; min-height:inherit;">
<h1 style="color: #4ABC08;">Checkout</h1>
<h3>You are about to donate $<?php echo $amount1; ?> to the user <?php echo $receiver; ?> for the fundraiser "<?php echo $title; ?>".</h3>
<?php echo $stripe; ?>
<h4><b>NOTE</b>: After the payment, you will not be able to refund your money.</h4>
	     
</div>
<div id="pageBottom" style="margin-top: 41%;">NoSettles Copyright &copy; 2015</div>
</body>
</html>