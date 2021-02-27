<?php 
  	
  if (isset($_POST['action']) && $_POST['action'] == "checkout") {	
	include_once("php_includes/check_login_status.php");
	$access_token = "";
	$account_id = "";
	$owner = $_POST['owner'];
	$donation = $_POST['donation'];
	$title = $_POST['title'];
	$statusid = $_POST['statusid'];
	if ($owner === $log_username) {
		echo "Unfortunately you cannot donate to yourself!";
		exit();
	}
	if ($donation >= 1 && is_numeric($donation)) {
		$sql = "SELECT * FROM users WHERE username='$owner' AND activated='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		  $access_token = $row['access'];
		  $account_id = $row['account_id'];
		}
		require 'wepay.php';
	
		// application settings
		$client_id = 119239;
		$client_secret = "cdf3ea984b";
	
		// change to useProduction for live environments
		Wepay::useStaging($client_id, $client_secret);
	
		$wepay = new WePay($access_token);
	
		// create the checkout
		$response = $wepay->request('checkout/create', array(
			'account_id'        => $account_id,
			'amount'            => ''.$donation.'',
			'short_description' => ''.$title.'',
			'type'              => 'DONATION',
			'mode'              => 'iframe',
			'redirect_uri'      => 'https://nosettles.com/checkout_done.php?status=success&id='.$statusid.'&amount='.$donation.'&user='.$owner.'',
		));
	
		// display the response
		$checkout_id = $response->checkout_id;
		$checkout_uri = $response->checkout_uri;
		echo "success|$checkout_uri|$checkout_id";
		// display the response
		exit();
	} else {
		echo "Something went wrong. Please try again.";
	}
  }
?>