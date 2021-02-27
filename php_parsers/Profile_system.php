<?php
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
?><?php
if (isset($_POST['action']) && $_POST['action'] == "send"){
	
	$image = preg_replace('#[^a-z0-9.]#i', '', $_POST['image']);
	
	// Insert the status post into the database now
	mysqli_query($db_conx, "UPDATE users SET avatar='$image' WHERE username='$log_username' LIMIT 1");
	
	// Count posts of type "a" for the person posting and evaluate the count
	$sql = "SELECT COUNT(id) FROM status WHERE author='$log_username' AND type='a'";
    $query = mysqli_query($db_conx, $sql); 
	$row = mysqli_fetch_row($query);
	
	
	/////////////////////////////////////////////////////////////////////////////////////
	
	if ($row[0] > 9) { // If they have 10 or more posts of type a
		// Delete their oldest post if you want a system that auto flushes the oldest
		// (you can auto flush for post types c and b if you wish to also)
		$sql = "SELECT id FROM status WHERE author='$log_username' AND type='a' ORDER BY id ASC LIMIT 1";
    	$query = mysqli_query($db_conx, $sql); 
		$row = mysqli_fetch_row($query);
		$oldest = $row[0];
		mysqli_query($db_conx, "DELETE FROM status WHERE osid='$oldest'");
  	}
	
	/////////////////////////////////////////////////////////////////////

	mysqli_close($db_conx);
	echo "yiss|$image";
	exit();
} else {
	echo $_POST['action'];
}
?>