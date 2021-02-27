<?php
$output = "";
if(isset($_POST['u'])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);	
	if ($u == ""){
		// They tried to defeat our security
		echo $output;
		exit;		
	}
	include("php_includes/db_conx.php");	
	$sql = "SELECT * FROM users 
	        WHERE username LIKE '%$u%' 
			ORDER BY username ASC LIMIT 10";
	$user_query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($user_query);
	if($numrows > 0){
		while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)){
			$uname = $row["username"];
			$Savatar = $row["avatar"];
			$avatar_search = "<img src='Profile_P/".$Savatar."' class='CPP' alt='Profile Pic' />";
			if ($Savatar == "") {
				$avatar_search = "<img src='style/DPP.png' class='CPP' alt='Profile Pic' />";
			}
			$output .= '<a href="user.php?u='.$uname.'" style="color: #09F;"><li class="searchLi">'.$avatar_search.''.$uname.'</li></a>';
		}
		echo $output;
		exit;
	} else {
		// No results from search
		echo $output;
		exit;
	}
}
?>