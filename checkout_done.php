<?php 
if(isset($_GET['id'])) {
  include_once("php_includes/check_login_status.php");
  $id = $_GET['id'];
  
  $sql = "SELECT * FROM donation WHERE id='$id' LIMIT 1";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  $checknumrows = mysqli_num_rows($query);
  if ($checknumrows > 0) {
	  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			  $price = $row['price'];
			  $receiver = $row['receiver'];
			  $status_id = $row['status_id'];
			  $title = $row['title'];
			  $amount = $price / 100;
	  }
  } else {
	header('location: https://nosettles.com/');
	exit();	  
  }
  $sql = "SELECT access_token FROM users WHERE username='$receiver' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $access_token = $row['access_token'];
  }

	require_once('stripe/init.php');  
    \Stripe\Stripe::setApiKey($access_token);

	// Get the credit card details submitted by the form
	$token = $_POST['stripeToken'];
	
	// Create the charge on Stripe's servers - this will charge the user's card
	try {
	$charge = \Stripe\Charge::create(array(
	  "amount" => $price, // amount in cents, again
	  "currency" => "USD",
	  "source" => $token,
	  "description" => "".$title."")
	);
	} catch(\Stripe\Error\Card $e) {
	  // The card has been declined
	}

  if ($log_username == "") {
  $sql = "INSERT INTO Checkouts (receiver, donor, amount, Time)       
		  VALUES('$receiver','anonymous','$amount',now())";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  $check_id = mysqli_insert_id($db_conx);
  } else {
  $sql = "INSERT INTO Checkouts (receiver, donor, amount, Time)       
		  VALUES('$receiver','$log_username','$amount',now())";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  $check_id = mysqli_insert_id($db_conx);	  
  }
  
  $sql = "SELECT * FROM status WHERE id='$status_id' LIMIT 1";
  $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $received = $row['received'];
  }
  $total = $amount + $received;
  $sql = "UPDATE status SET received='$total' WHERE id='$status_id' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  
  $avatar = "";
  if ($log_username != "") {
	$sql = "SELECT * FROM users WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	  $Donated = $row['donated'];
	  $avatar = $row['avatar'];
	}
    $total = $amount + $Donated;
    $sql = "UPDATE users SET donated='$total' WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
	 
    $avatar_notif = '<img src="Profile_P/'.$avatar.'" class="CPP" alt="Profile Pic" />';
    if ($avatar == "") {
    	$avatar_notif = '<img src="style/DPP.png" class="CPP" alt="Profile Pic" />';
    } 
  }
  if ($log_username == "") {
	  $avatar_notif = '<img src="style/DPP.png" class="CPP" alt="Profile Pic" />';
	  $log_username = "Someone anonymous";
  }
  ///////////////////////////NOTIFICATION/////////////////////////////////////////
  $app = "Donation";
  $note = '<a href="received.php" style="color: #09F;"><img src="style/donation.png" alt="Donation" class="icon_donation" />'.$avatar_notif.'<img src="style/donation.png" alt="Donation" class="icon_donation" /><li class="Notification">'.$log_username.' donated $'.$amount.' for your post '.$title.'.</li></a>';			 
  
  if ($log_username == "") {
  mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) 
		             VALUES('$receiver','anonymous','$app','$note',now())") or die(mysqli_error($db_conx));
  } elseif($log_username != "") {
  mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) 
		             VALUES('$receiver','$log_username','$app','$note',now())") or die(mysqli_error($db_conx));	  
  }
  
  header('location: https://nosettles.com/donation_done.php?check_id='.$check_id.'');
  exit();
} else {
	header('location: https://nosettles.com/');
	exit();
}
?>
