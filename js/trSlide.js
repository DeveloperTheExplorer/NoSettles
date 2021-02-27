function slideIn(el){
	var elem = document.getElementById(el);
	elem.style.transition = "left 0.5s ease-in 0s";
	elem.style.left = "0px";
}
function slideOut(el){
	var elem = document.getElementById(el);
	elem.style.transition = "left 0.5s ease-out 0s";
	elem.style.left = "-306px";
}

//html
//<button onclick="slideIn('box1');">slide in</button>
//<button onclick="slideOut('box1');">slide out</button>

//css
//div#box1 {
//	background: #9DCEFF;
//	width: 400px;
//	height: 200px;
//	position: absolute;
//	top: 50px;
//	left: -400px;
//}