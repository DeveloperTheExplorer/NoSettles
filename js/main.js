function _(x) {
	return document.getElementById(x);
}

function getNames(u){	
	var rx = new RegExp;
	rx = /[^a-z0-9]/gi;	
	var replaced = u.search(rx) >= 0;
	if(replaced){
    	u = u.replace(rx, "");
		document.getElementById("searchUsername").value = u;
	}	
	if(u == ""){
		document.getElementById("memSearchResults").style.display = "none";
		return false;
	}
	// http://www.developphp.com/view.php?tid=1185
    var hr = new XMLHttpRequest();
    hr.open("POST", "seach_executive.php", true);
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    hr.onreadystatechange = function() {
		    var return_data = hr.responseText;
			if(return_data != ""){	
				document.getElementById("memSearchResults").style.display = "block"; 
				document.getElementById("memSearchResults").innerHTML = return_data;
			}
	    
    }
    hr.send("u="+u);
}
window.addEventListener('mouseup', function(event){
	var box = document.getElementById('memSearchResults');
	if (event.target != box && event.target.parentNode != box){
        box.style.display = 'none';
		document.getElementById("searchUsername").value = "";
    }
});