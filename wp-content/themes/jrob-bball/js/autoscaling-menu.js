function initPage()
{
	initAutoScalingNav({
		menuId: "main-nav",
		sideClasses: true,
		tag: "a"
	});
}
function initAutoScalingNav(o)
{
	if (!o.menuId) o.menuId = "main-nav";
	if (!o.tag) o.tag = "a";
	if (!o.spacing) o.spacing = 0;
	if (!o.constant) o.constant = 0;
	if (!o.minPaddings) o.minPaddings = 0;
	if (!o.liHovering) o.liHovering = false;
	if (!o.sideClasses) o.sideClasses = false;
	var nav = document.getElementById(o.menuId);
	if(nav)
	{
		var lis = nav.getElementsByTagName("li");
		var asFl = [];
		var lisFl = [];
		for (var i=0, j=0; i<lis.length; i++)
		{
			if(lis[i].parentNode == nav)
			{
				var t = lis[i].getElementsByTagName(o.tag).item(0);
				asFl.push(t);
				asFl[j++].width = t.offsetWidth;
				lisFl.push(lis[i]);
			}
			if(o.liHovering)
			{
				lis[i].onmouseover = function()
				{
					this.className += " hover";
				}
				lis[i].onmouseout = function()
				{
					this.className = this.className.replace("hover", "");
				}
			}
		}
		var menuWidth = nav.clientWidth - asFl.length*o.spacing - o.constant;
		if(getItemsWidth(asFl) < menuWidth)
		{
			for (var i=0; getItemsWidth(asFl) < menuWidth; i++)
			{
				asFl[i].width++;
				if(i >= asFl.length-1) i=-1;
			}
			for (var i=0; i<asFl.length; i++)
			{
				asFl[i].style.width = asFl[i].width + "px";
			}
		}
		else if(o.minPaddings > 0)
		{
			for (var i=0; i<asFl.length; i++)
			{
				asFl[i].style.width = asFl[i].width + o.minPaddings*2 + "px";
			}
		}
	}
	function getItemsWidth(a)
	{
		var w = 0;
		for(var q=0; q<a.length; q++)
		{
			w += a[q].width;
		}
		return w;
	}
	if(o.sideClasses)
	{
		lisFl[0].className += " first-child";
		lisFl[lisFl.length-1].className += " last-child";
	}
}
if (window.addEventListener)
	window.addEventListener("load", initPage, false);
else if (window.attachEvent)
	window.attachEvent("onload", initPage);