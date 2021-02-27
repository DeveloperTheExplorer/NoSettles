<?php
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
?><?php
if (isset($_POST['action']) && $_POST['action'] == "status_post"){
	// Make sure post data is not empty
	if($_POST['title'] == "" || $_POST['amount'] == "" || $_POST['data'] == ""){
		mysqli_close($db_conx);
	    echo "data_empty";
	    exit();
	}
	// Make sure type is a
	if($_POST['type'] != "a"){
		mysqli_close($db_conx);
	    echo "type_unknown";
	    exit();
	}
	$image = preg_replace('#[^a-z0-9.]#i', '', $_POST['image']);
	//moving the file to the permanent folder
	if ($image != "na") {
		$kaboom = explode(".", $image);
		$fileExt = end($kaboom);
		rename("../tempUploads/$image", "../permUploads/$image");
		include_once("../php_includes/image_resize.php");
		$target_file = "../permUploads/$image";
		$resized_file = "../permUploads/$image";
		$wmax = 1200;
		$hmax = 1200;
		list($width, $height) = getimagesize($target_file);
		if ($width > $wmax || $height > $hmax) {
			img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
		}
	}
	// Clean all of the $_POST vars that will interact with the database
	$type = preg_replace('#[^a-z]#', '', $_POST['type']);
	$account_name = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
	$title = htmlentities($_POST['title']);
	$title = mysqli_real_escape_string($db_conx, $title);
	$amount = preg_replace('#[^a-z0-9]#i', '', $_POST['amount']);
	$image = preg_replace('#[^a-z0-9.]#i', '', $_POST['image']);
	$data = htmlentities($_POST['data']);
	$data = mysqli_real_escape_string($db_conx, $data);
	// Make sure account name exists (the profile being posted on)
	$sql = "SELECT COUNT(id) FROM users WHERE username='$account_name' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	if($row[0] < 1){
		mysqli_close($db_conx);
		echo "$account_no_exist";
		exit();
	}
	$img ='<img src="permUploads/'.$image.'" class="status_img" />';
	
	// Insert the status post into the database now
	$sql = "INSERT INTO status(account_name, author, type, title, amount, data, image, postdate) 
			VALUES('$account_name','$log_username','$type','$title','$amount','$data','$img',now())";
	$query = mysqli_query($db_conx, $sql);
	$id = mysqli_insert_id($db_conx);
	mysqli_query($db_conx, "UPDATE status SET osid='$id' WHERE id='$id' LIMIT 1");

	mysqli_close($db_conx);
	echo "post_ok|$id";
	exit();
}
?><?php 
//action=status_reply&osid="+osid+"&user="+user+"&data="+data
if (isset($_POST['action']) && $_POST['action'] == "status_reply"){
	// Make sure data is not empty
	if(strlen($_POST['data']) < 1){
		mysqli_close($db_conx);
	    echo "data_empty";
	    exit();
	}
	// Clean the posted variables
	$osid = preg_replace('#[^0-9]#', '', $_POST['sid']);
	$account_name = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
	$data = htmlentities($_POST['data']);
	$data = mysqli_real_escape_string($db_conx, $data);
	// Make sure account name exists (the profile being posted on)
	$sql = "SELECT COUNT(id) FROM users WHERE username='$account_name' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	if($row[0] < 1){
		mysqli_close($db_conx);
		echo "$account_no_exist";
		exit();
	}
	// Insert the status reply post into the database now
	$sql = "INSERT INTO status(osid, account_name, author, type, data, postdate)
	        VALUES('$osid','$account_name','$log_username','b','$data',now())";
	$query = mysqli_query($db_conx, $sql);
	$id = mysqli_insert_id($db_conx);
	// Insert notifications for everybody in the conversation except this author
	$sql = "SELECT author FROM status WHERE osid='$osid' AND author!='$log_username' GROUP BY author";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$participant = $row["author"];
		$app = "Status Reply";
		$note = $log_username.' commented here:<br /><a href="user.php?u='.$account_name.'#status_'.$osid.'">Click here to view the conversation</a>';
		mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) 
		             VALUES('$participant','$log_username','$app','$note',now())");
	}
	
	$sql = "SELECT avatar FROM users WHERE username='$log_username' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$avatar = $row["avatar"];
	}
	if ($avatar == "") {
		$CPP = '<img src="style/DPP.png" class="CPP" alt="Profile Pic" />';
	} else {
		$CPP = "<img src='Profile_P/".$avatar."' class='CPP' alt='Profile Pic' />";
	}
	
	mysqli_close($db_conx);
	echo "reply_ok|$id|$CPP";
	exit();
}
?><?php 
if (isset($_POST['action']) && $_POST['action'] == "delete_status"){
	if(!isset($_POST['statusid']) || $_POST['statusid'] == ""){
		mysqli_close($db_conx);
		echo "status id is missing";
		exit();
	}
	$statusid = preg_replace('#[^0-9]#', '', $_POST['statusid']);
	// Check to make sure this logged in user actually owns that comment
	$query = mysqli_query($db_conx, "SELECT account_name, author FROM status WHERE id='$statusid' LIMIT 1");
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$account_name = $row["account_name"]; 
		$author = $row["author"];
	}
    if ($author == $log_username || $account_name == $log_username) {
		mysqli_query($db_conx, "DELETE FROM status WHERE osid='$statusid'");
		mysqli_close($db_conx);
	    echo "delete_ok";
		exit();
	}
}
?><?php 
if (isset($_POST['action']) && $_POST['action'] == "delete_status"){
	if(!isset($_POST['statusid']) || $_POST['statusid'] == ""){
		mysqli_close($db_conx);
		echo "status id is missing";
		exit();
	}
	$statusid = preg_replace('#[^0-9]#', '', $_POST['statusid']);
	// Check to make sure this logged in user actually owns that comment
	$query = mysqli_query($db_conx, "SELECT account_name, author, data FROM status WHERE id='$statusid' LIMIT 1");
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$account_name = $row["account_name"]; 
		$author = $row["author"];
		$data = $row["data"];
	}
    if ($author == $log_username || $account_name == $log_username) {
		if(preg_match('/<img.+src=[\'"](?P<src>.+)[\'"].*>/i', $data, $has_image)){
			$source = '../'.$has_image['src'];
			if (file_exists($source)) {
        		unlink($source);
    		}
		}
		mysqli_query($db_conx, "DELETE FROM status WHERE osid='$statusid'");
		mysqli_close($db_conx);
	    echo "delete_ok";
		exit();
	}
}
?><?php 
if (isset($_POST['action']) && $_POST['action'] == "delete_reply"){
	if(!isset($_POST['replyid']) || $_POST['replyid'] == ""){
		mysqli_close($db_conx);
		exit();
	}
	$replyid = preg_replace('#[^0-9]#', '', $_POST['replyid']);
	// Check to make sure the person deleting this reply is either the account owner or the person who wrote it
	$query = mysqli_query($db_conx, "SELECT osid, account_name, author FROM status WHERE id='$replyid' LIMIT 1");
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$osid = $row["osid"];
		$account_name = $row["account_name"];
		$author = $row["author"];
	}
    if ($author == $log_username || $account_name == $log_username) {
		mysqli_query($db_conx, "DELETE FROM status WHERE id='$replyid'");
		mysqli_close($db_conx);
	    echo "delete_ok";
		exit();
	}
}
?>