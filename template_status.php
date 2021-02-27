<?php
$statuslist = "";
	$status_ui = '<div class="NP">';
	  $status_ui .= '<div class="npLeft">';
		$status_ui .= '<h3>Your title:</h3>';
		$status_ui .= '<input type="text" id="title" class="newPost npBox1" placeholder="Your interesting title" />';
	  $status_ui .= '</div>';
	  $status_ui .= '<div class="npRight">';
		$status_ui .= '<h3>Amount:</h3>';
		$status_ui .= '<br />';
		$status_ui .= '<input type="number" id="amount" class="newPost npBox2" />';
		$status_ui .= '<span style="font-size: 42px; color: #06B0FF; float: right; margin-right: 2px;">$</span>';
	  $status_ui .= '</div>';
	  $status_ui .= '<div class="npLeft">';
		$status_ui .= '<br />';
		$status_ui .= '<br />';
		$status_ui .= '<h3>Description:</h3>';
		$status_ui .= '<textarea id="data" class="newPost npBox3" placeholder="Try to be completely honest and write from your heart! Take your time and write the best description you can think of. Goodluck!">';
		$status_ui .= '</textarea>';
	  $status_ui .= '</div>';
	  $status_ui .= '<div class="npRight">';
	    $status_ui .= '<br />';
		$status_ui .= '<br />';
		$status_ui .= '<h3>Upload image <div id="help" style="font-size: 16px;margin-top: -3px;width: 10px;text-align: center;" onclick="fadeIn(\'invite\',\'invite_pop\'); info()">i</div></h3>';
		$status_ui .= '<br />';
		$status_ui .= '<div id="uploadDisplay_SP">';
		$status_ui .= '<img class="npImage" id="imgUpload" src="style/blue-camera-icon.png" /></div>';
		$status_ui .= '<div class="upload">';
		  $status_ui .= '<form id="image_SP" enctype="multipart/form-data" method="post">';
		    $status_ui .= '<input accept="image/*" type="file" name="FileUpload" id="fu_SP" onchange="doUpload(\'fu_SP\')"/>';
		  $status_ui .= '</form>';
		$status_ui .= '</div>';
		$status_ui .= '<div class="uploadBtn" id="triggerBtn_SP" class="triggerBtn" onclick="triggerUpload(event, \'fu_SP\')">Upload Photo </div>';
		
	  $status_ui .= '</div>';
	  $status_ui .= '<button id="statusBtn" class="newPost npBttn" onclick="postToStatus(\'status_post\',\'a\',\''.$log_username.'\',\'title\',\'amount\',\'data\')">Post</button><br /><div id="message"></div></div>';
?><?php 
$sql = "SELECT * FROM status WHERE account_name='$u' AND type='a' ORDER BY postdate DESC LIMIT 20";
$query = mysqli_query($db_conx, $sql);
$statusnumrows = mysqli_num_rows($query);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$statusid = $row["id"];
	$account_name = $row["account_name"];
	$author = $row["author"];
	$postdate = $row["postdate"];
	$amount = $row["amount"];
	$amount = nl2br($amount);
	$amount = str_replace("&amp;","&",$amount);
	$amount= stripslashes($amount);
	$title = $row["title"];
	$title = nl2br($title);
	$title = str_replace("&amp;","&",$title);
	$title= stripslashes($title);
	$data = $row["data"];
	$data = nl2br($data);
	$data = str_replace("&amp;","&",$data);
	$data = stripslashes($data);
	$statusDeleteButton = '';
	if($author == $log_username || $account_name == $log_username ){
		$statusDeleteButton = '<span id="sdb_'.$statusid.'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\');" title="DELETE THIS STATUS AND ITS REPLIES">delete status</a></span> &nbsp; &nbsp;';
	}
	// GATHER UP ANY STATUS REPLIES
	$status_replies = "";
	$query_replies = mysqli_query($db_conx, "SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
	$replynumrows = mysqli_num_rows($query_replies);
    if($replynumrows > 0){
        while ($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)) {
			$statusreplyid = $row2["id"];
			$replyauthor = $row2["author"];
			$replydata = $row2["data"];
			$replydata = nl2br($replydata);
			$replypostdate = $row2["postdate"];
			$replydata = str_replace("&amp;","&",$replydata);
			$replydata = stripslashes($replydata);
			$replyDeleteButton = '';
			if($replyauthor == $log_username || $account_name == $log_username ){
				$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">remove</a></span>';
			}
			$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes"><div><b>Reply by <a href="user.php?u='.$replyauthor.'">'.$replyauthor.'</a> '.$replypostdate.':</b> '.$replyDeleteButton.'<br />'.$replydata.'</div></div>';
        }
    }
	$statuslist .= '<div id="status_'.$statusid.'" class="status_boxes"><div><b>Posted by <a href="user.php?u='.$author.'">'.$author.'</a> '.$postdate.':</b> '.$statusDeleteButton.' <br />'.$data.'</div>'.$status_replies.'</div>';
	if($isFriend == true || $log_username == $u){
	    $statuslist .= '<textarea id="replytext_'.$statusid.'" class="replytext" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button id="replyBtn_'.$statusid.'" onclick="replyToStatus('.$statusid.',\''.$u.'\',\'replytext_'.$statusid.'\',this)">Reply</button>';	
	}
}
?>
<script>
var hasImage = "";
window.onbeforeunload = function(){
	if(hasImage !== ""){
	    return "You have not posted your image";
	}
};
function doUpload(id){
	// www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
	var file = _(id).files[0];
	if(file.name === ""){
		return false;		
	}
	if(file.type != "image/jpeg" && file.type != "image/gif" && file.type != "image/png"){
		alert("That file type is not supported.");
		return false;
	}
	_("uploadDisplay_SP").innerHTML = "<div id='outer'></div>";
	var formdata = new FormData();
	formdata.append("stPic", file);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "php_parsers/photo_system.php");
	ajax.send(formdata);	
}
function progressHandler(event) {
	var percent = (event.loaded / event.total) * 100;
	_("outer").innerHTML = "<div id='inner'>"+percent+"%</div>";
	_("inner").style.width = percent+'%';
}
function completeHandler(event){
	var data = event.target.responseText;
	var datArray = data.split("|");
	if(datArray[0] == "upload_complete"){
		hasImage = datArray[1];
		_("uploadDisplay_SP").innerHTML = '<img src="tempUploads/'+datArray[1]+'" class="statusImage" />';
		_("imgUpload").style.display = "none";
	} else {
		_("uploadDisplay_SP").innerHTML = datArray[0];
		_("triggerBtn_SP").style.display = "block";
	}
}
function errorHandler(event){
	_("uploadDisplay_SP").innerHTML = "Upload Failed";
	_("triggerBtn_SP").style.display = "block";
}
function abortHandler(event){
	_("uploadDisplay_SP").innerHTML = "Upload Aborted";
	_("triggerBtn_SP").style.display = "block";
}
function triggerUpload(e,elem){
	e.preventDefault();
	_(elem).click();	
}
function postToStatus(action,type,user,ti, am, ta){
	var title = _(ti).value;
	var amount = _(am).value;
	var data = _(ta).value;
	if(amount === "" || title === "" || data === ""){
		alert("Please fill in all of the fields!");
		return false;
	}
	var data2 = "";
	if(data !== ""){
		data2 = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/n/g,"<br />").replace(/r/g,"<br />");
	}
	if (data2 === "" && hasImage !== ""){
		data = "||na||";
		data2 = '<img src="permUploads/'+hasImage+'" />';		
	} else if (data2 !== "" && hasImage !== ""){
		data2 += '<br /><img src="permUploads/'+hasImage+'" />';
	} else {
		hasImage = "na";
	}
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
			if(datArray[0] == "post_ok"){
				var sid = datArray[1];
				title = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				amount = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				/*var currentHTML = _("statusarea").innerHTML;
				_("statusarea").innerHTML = '<div id="status_'+sid+'" class="status_boxes"><div><b>Posted by you just now:</b> <span id="sdb_'+sid+'"><a href="#" onclick="return false;" onmousedown="deleteStatus(''+sid+'','status_'+sid+'');" title="DELETE THIS STATUS AND ITS REPLIES">delete status</a></span><br />'+data2+'</div></div><textarea id="replytext_'+sid+'" class="replytext" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button id="replyBtn_'+sid+'" onclick="replyToStatus('+sid+','<?php /*echo $u;*/ ?>','replytext_'+sid+'',this)">Reply</button>'+currentHTML;*/
				_(ti).value = "";
				_(am).value = "";
				_(ta).value = "";
				_("uploadDisplay_SP").innerHTML = "<img class='npImage' id='imgUpload' src='style/blue-camera-icon.png' />";
				_("fu_SP").value = "";
				hasImage = "";
				_("message").innerHTML = "<a class='message' href='/'>Click here to view your post!</a>";
			} else {
				alert(ajax.responseText);
				alert('Somting wong!');
			}
		}
	};
	ajax.send("action="+action+"&type="+type+"&user="+user+"&title="+title+"&amount="+amount+"&data="+data+"&image="+hasImage);
}
function replyToStatus(sid,user,ta,btn){
	var data = _(ta).value;
	if(data === ""){
		alert("Type something first weenis");
		return false;
	}
	_("replyBtn_"+sid).disabled = true;
	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
			if(datArray[0] == "reply_ok"){
				var rid = datArray[1];
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
				_("status_"+sid).innerHTML += '<div id="reply_'+rid+'" class="reply_boxes"><div><b>Reply by you just now:</b><span id="srdb_'+rid+'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''+rid+'\',\'reply_'+rid+'\');" title="DELETE THIS COMMENT">remove</a></span><br />'+data+'</div></div>';
				_("replyBtn_"+sid).disabled = false;
				_(ta).value = "";
			} else {
				alert(ajax.responseText);
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
				_(statusbox).style.display = 'none';
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
				_(replybox).style.display = 'none';
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
function info() {
	_('invite_code').style.visibility = "hidden";
	_('invite_div').innerHTML = "";
	_('invite_h2').innerHTML = "Image Size";
	_('invite_p').innerHTML = 'Please make sure that your image is around or proportionally same as 500px(width) by 450px(height), otherwise they might not look good.';
}
</script>
  <?php echo $status_ui; ?>
