function initPage()
{
	initAutoScalingNav({
		menuId: "main-nav",
		sideClasses: true,
		tag: "a"
	});
}

function initAutoScalingNav(C){if(!C.menuId){C.menuId="main-nav"}if(!C.tag){C.tag="a"}if(!C.spacing){C.spacing=0}if(!C.constant){C.constant=0}if(!C.minPaddings){C.minPaddings=0}if(!C.liHovering){C.liHovering=false}if(!C.sideClasses){C.sideClasses=false}var A=document.getElementById(C.menuId);if(A){var J=A.getElementsByTagName("li");var H=[];var D=[];for(var F=0,E=0;F<J.length;F++){if(J[F].parentNode==A){var I=J[F].getElementsByTagName(C.tag).item(0);H.push(I);H[E++].width=I.offsetWidth;D.push(J[F])}if(C.liHovering){J[F].onmouseover=function(){this.className+=" hover"};J[F].onmouseout=function(){this.className=this.className.replace("hover","")}}}var G=A.clientWidth-H.length*C.spacing-C.constant;if(B(H)<G){for(var F=0;B(H)<G;F++){H[F].width++;if(F>=H.length-1){F=-1}}for(var F=0;F<H.length;F++){H[F].style.width=H[F].width+"px"}}else{if(C.minPaddings>0){for(var F=0;F<H.length;F++){H[F].style.width=H[F].width+C.minPaddings*2+"px"}}}}function B(L){var K=0;for(var M=0;M<L.length;M++){K+=L[M].width}return K}if(C.sideClasses){D[0].className+=" first-child";D[D.length-1].className+=" last-child"}};

if (window.addEventListener)
	window.addEventListener("load", initPage, false);
else if (window.attachEvent)
	window.attachEvent("onload", initPage);