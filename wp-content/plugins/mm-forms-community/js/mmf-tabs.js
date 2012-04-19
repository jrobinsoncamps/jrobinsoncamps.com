function show_tab(sender,others,div,oth_divs)
{
	document.getElementById('mmf-save').style.display='block';
	overwrite_option(0);
	ids = document.getElementById(others).getElementsByTagName('li');
	for(i=0;i<ids.length;i++)
	{
		ids[i].className = '';
	}
	sender = document.getElementById(sender);
	sender.className = 'current';
	
	
	div_to_open = document.getElementById(div);
	div_to_open.style.display = 'block';
	
	divs_to_close = oth_divs.split(",");
	for(k=0;k<divs_to_close.length;k++)
	{
		document.getElementById(divs_to_close[k]).style.display = 'none';
	}
	
}