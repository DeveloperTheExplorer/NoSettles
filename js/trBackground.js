function changeBG(el,clr){
	var elem = document.getElementById(el);
	elem.style.transition = "background 1.0s linear 0s";
	elem.style.background = clr;
}

//html
//<button onclick="changeBG('box1','#F0F')">Magenta</button>

//css
//div#box1 {
//	background: #9DCEFF;
//	width: 400px;
//	height: 200px;
//}