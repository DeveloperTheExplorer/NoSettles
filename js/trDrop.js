function slideOpen(el){
	var elem = document.getElementById(el);
	elem.style.transition = "height 0.2s linear 0s";
	elem.style.height = "200px";
}
function slideClosed(el){
	var elem = document.getElementById(el);
	elem.style.transition = "height 0.2s linear 0s";
	elem.style.height = "0px";
}

//html
//<button onclick="slideClosed('box1');">slideClosed</button>

//css
//div#box1 {
//	background: #9DCEFF;
//	width: 400px;
//	height: 200px;
//	overflow: hidden;
//}