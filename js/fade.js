function fadeOut(el){
	var elem = document.getElementById(el);
	elem.style.transition = "opacity 0.5s linear 0s";
	elem.style.opacity = 0;
}
function fadeIn(el){
	var elem = document.getElementById(el);
	elem.style.transition = "opacity 0.5s linear 0s";
	elem.style.opacity = 1;
}
//html
//<button onclick="fadeOut('box1');">Fade out</button>

//css
//div#box1 {
//	background: #9DCEFF;
//	width: 400px;
//	height: 200px;
//}