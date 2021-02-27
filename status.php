<?php
$statuslist = "";
?><?php 
include_once("classes/develop_php_library.php");
include_once("php_includes/check_login_status.php");
$sql = "SELECT * FROM status WHERE author='$u' AND type='a' ORDER BY postdate DESC LIMIT 20";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
if($statusnumrows > 0){
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$statusid = $row["id"];
	$account_name = $row["account_name"];
	$author = $row["author"];
	$postdate = $row["postdate"];
	$timeAgoObject = new convertToAgo;
	$convertedTime = ($timeAgoObject -> convert_datetime($postdate)); // Convert Date Time
	$when = ($timeAgoObject -> makeAgo($convertedTime));
	$amount = $row["amount"];
	$amount = nl2br($amount);
	$amount = str_replace("&amp;","&",$amount);
	$amount= stripslashes($amount);
	$received = $row["received"];
	$received = nl2br($received);
	$received = str_replace("&amp;","&",$received);
	$received= stripslashes($received);
	$title = $row["title"];
	$title = nl2br($title);
	$title = str_replace("&amp;","&",$title);
	$title = stripslashes($title);
	$data = $row["data"];
	$data = nl2br($data);
	$data = str_replace("&amp;","&",$data);
	$data = stripslashes($data);
	$image = $row["image"];
	$image = nl2br($image);
	$image = str_replace("&amp;","&",$image);
	$image = stripslashes($image);
	$statusDeleteButton = '';
	if($author == $log_username || $account_name == $log_username ){
		$statusDeleteButton = '<span id="sdb_'.$statusid.'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\');" title="DELETE THIS STATUS AND ITS REPLIES">  delete post</a></span> &nbsp; &nbsp;';
	}
	if ($image == '<img src="permUploads/na" class="status_img" />') {
	  $image = '<img src="style/blue-camera-icon.png" class="status_img">';
	}
	$sql3 = "SELECT * FROM users WHERE username='$author' LIMIT 1";
	$query3 = mysqli_query($db_conx, $sql3);
	$statusnumrows3 = mysqli_num_rows($query3);
	while ($row3 = mysqli_fetch_array($query3, MYSQLI_ASSOC)) {
		$avatar = $row3["avatar"];
	}
	///////////////////////////////////////////////////
	// GATHER UP ANY STATUS REPLIES
	$status_replies = "";
	$query_replies = mysqli_query($db_conx, "SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate DESC");
	$replynumrows = mysqli_num_rows($query_replies);
    if($replynumrows > 0){
        while ($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)) {
			$statusreplyid = $row2["id"];
			$replyauthor = $row2["author"];
			$replydata = $row2["data"];
			$replydata = nl2br($replydata);
			$replypostdate = $row2["postdate"];
			$timeAgoObject = new convertToAgo;
			$convertedTime = ($timeAgoObject -> convert_datetime($replypostdate)); // Convert Date Time
			$when1 = ($timeAgoObject -> makeAgo($convertedTime));
			$replydata = str_replace("&amp;","&",$replydata);
			$replydata = stripslashes($replydata);
			$replyDeleteButton = '';
			if($replyauthor == $log_username || $account_name == $log_username ){
				$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">remove</a></span>';
			}
			//avatars for replies...
			$sql2 = "SELECT * FROM users WHERE username='$replyauthor' AND activated='1' LIMIT 1";
			$log_user_query2 = mysqli_query($db_conx, $sql2);
			$numrows2 = mysqli_num_rows($log_user_query2);
			while ($row2 = mysqli_fetch_array($log_user_query2, MYSQLI_ASSOC)) {
				$avatar5 = $row2["avatar"];
			}
			if ($avatar5 == "") {
				$CPP = '<img src="style/DPP.png" class="CPP" alt="Profile Pic" />';
			} else {
				$CPP = "<img src='Profile_P/".$avatar5."' class='CPP' alt='Profile Pic'>";
			}
			$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes">'.$CPP.'<div id="comment"><b>Reply by <a href="user.php?u='.		$replyauthor.'" target="_blank">'.$replyauthor.'</a> '.$when1.':</b> '.$replyDeleteButton.'<br />'.$replydata.'</div></div>';
        }
    } else {
		$status_replies = "<div id='no_c'> No comments found, be the first one!</div>";
	}
	//avatars for posts...
	$sql3 = "SELECT * FROM users WHERE username='$author' AND activated='1' LIMIT 1";
	$log_user_query3 = mysqli_query($db_conx, $sql3);
	$numrows3 = mysqli_num_rows($log_user_query3);
	while ($row3 = mysqli_fetch_array($log_user_query3, MYSQLI_ASSOC)) {
		$avatar8 = $row3["avatar"];
	}
	if ($avatar8 == "") {
		$DDP2 = '<img src="style/DPP.png" id="DDP2" alt="Profile Pic" />';
	} else {
		$DDP2 = "<img src='Profile_P/".$avatar8."' id='DDP2' alt='Profile Pic'>";
	}
		

	$total = $received / $amount;
	$percent = $total * 100;
	$percentage = round($percent, 0, PHP_ROUND_HALF_UP);
	if ($percent > 100) {
		$percent = 100;
	} else if ($received == 0) {
		$percentage = 0;
		$percent = 0;
	}
	$amount1 = number_format($amount);
	
	
	///////////
	$statuslist .= '<div id="status_'.$statusid.'"><div id="status"><div id="status_post"><a href="user.php?u='.$author.'" style="font-size: 24px;font-weight: bold;color: #0DB3FF;">'.$DDP2.''.$author.'</a><div class="top_center">'.$title.'</div><div class="status_date"> '.$when.''.$statusDeleteButton.'</div><div class="top_right"><img src="style/ajax-loader.gif" id="load_'.$statusid.'" class="load" alt="Loading..." /><button type="button" class="donate_ntn" id="button_'.$statusid.'" onclick="donate(\'amount_'.$statusid.'\',\''.$author.'\',\''.$title.'\',\''.$statusid.'\')">Donate</button><input type="number" name="donation" placeholder="Amount" class="dnt_amount" id="amount_'.$statusid.'"/><div class="amount"><p style="margin: 0;font-weight: normal;">$'.$amount1.'</p><div class="status_outer"><div class="status_inner" style="width: '.$percent.'%;"></div></div><p style="margin: 0;font-weight: normal;">'.$percentage.'% Done</p></div></div><br /><div class="RHD"><div class="center_left">'.$data.'</div></div><div class="center_right">'.$image.'</div><div id="scroller_'.$statusid.'" class="RHD" style="text-align: center; margin-top: 5px;"><div id="comment_'.$statusid.'" class="comments"><div id="comment1_'.$statusid.'"></div>'.$status_replies.'</div></div><div id="st_ok_'.$statusid.'"></div></div>';
	    
		$statuslist .= '<textarea id="status_comment_'.$statusid.'" class="replytext status_comment" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button id="st_button" onclick="replyToStatus('.$statusid.',\''.$log_username.'\',\'status_comment_'.$statusid.'\',this, \'st_ok_'.$statusid.'\')">Reply</button></div></div>';	
}
} else {
	$statuslist = '<div id="No_post">No posts found.</div>';
}
?>
<script type="text/javascript">
function replyToStatus(sid,user,ta,btn, st_ok){
	var data = _(ta).value;
	var comment = "comment_"+sid;
	var comment1 = "comment1_"+sid;
	var scroller = "scroller_"+sid;
	if(data === ""){
		alert("Please type something first!");
		return false;
	}
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
			if(datArray[0] == "reply_ok"){
				var rid = datArray[1];
				var avatar = datArray[2];
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				_(ta).value = "";
				_(scroller).scrollTop = 0;
				_(comment1).innerHTML += '<div id="comment1_'+sid+'"></div><div id="reply_'+rid+'" class="reply_boxes">'+avatar+'<div id="comment"><b>Reply by you just now:</b><span id="srdb_'+rid+'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''+rid+'\',\'reply_'+rid+'\');" title="DELETE THIS COMMENT">remove</a></span><br />'+data+'</div></div>';
				  _("no_c").innerHTML = "";
			} else if (datArray[0] == "fail") {
				alert("The action function is not working properly");
			} else {
				alert("PHP did not launch!");
			}
		}
	};
	ajax.send("action=status_reply&sid="+sid+"&user="+user+"&data="+data);
}
function deleteStatus(statusid,statusbox){
	var conf = confirm("Press OK to confirm deletion of this status and its replies");
	if(conf !== true){
		return false;
	}
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "delete_ok"){
				_(statusbox).innerHTML = '<div id="No_post">Post deleted.</div>';
				_("replytext_"+statusid).style.display = 'none';
				_("replyBtn_"+statusid).style.display = 'none';
			} else {
				alert(ajax.responseText);
			}
		}
	};
	ajax.send("action=delete_status&statusid="+statusid);
}
function deleteReply(replyid,replybox){
	var conf = confirm("Press OK to confirm deletion of this reply");
	if(conf !== true){
		return false;
	}
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "delete_ok"){
				_(replybox).innerHTML = 'Comment deleted.';
				_(replybox).style.borderBottom = "border-bottom: 4px solid #fff";
			} else {
				alert(ajax.responseText);
			}
		}
	};
	ajax.send("action=delete_reply&replyid="+replyid);
}
function statusMax(field, maxlimit) {
	if (field.value.length > maxlimit){
		alert(maxlimit+" maximum character limit reached");
		field.value = field.value.substring(0, maxlimit);
	}
}
var loading;
var button;
var email = "";
var publish = "";
var title = "";

</script>
  <?php echo $statuslist; ?>
