
(function() {
	tinymce.create('tinymce.plugins.mmforms', {
		
		init : function(ed, url) {
			var t = this;
			var menuData;
			t.url = url;
			t.editor = ed;
			ed.addCommand('mmForms', function() {
          _sndInsertReq('mmForms',url);
			});
			
			ed.addCommand('mmForm', function() {
          _sndInsertReq('mmForm',url);
			});
			
			// Register mmForms button
			ed.addButton('mmForms', {
				title : 'Insert MM-Forms Submit Data',
				cmd : 'mmForms',
				myurl:url,				
				image : url + '/images/mm-forms.gif',
				onclick : function(ed, myurl) 
				{
						var t = this;						
						////////////Get Data Using Ajax///////////
						var xmlHttp;
						var AdFirst;
						var DIVNAME = "Next";
						var DIVNAME1 = "Type";
						var requestURL;
						var is_ie = (navigator.userAgent.indexOf('MSIE') >= 0) ? 1 : 0; 
						var is_ie5 = (navigator.appVersion.indexOf("MSIE 5.5")!=-1) ? 1 : 0; 
						var is_opera = ((navigator.userAgent.indexOf("Opera 6")!=-1)||(navigator.userAgent.indexOf("Opera/6")!=-1)) ? 1 : 0; 
						var is_netscape = (navigator.userAgent.indexOf('Netscape') >= 0) ? 1 : 0;
						var INCT;
						var SubInd;
						
						function fill_category_detail(url)
						{  
								requestURL =url + "/includes/rpc.php";
								var url = requestURL;
								xmlHttp = GetXmlHttpObject(stateChangeHandler1);
								xmlHttp_Get(xmlHttp, url);
						}
						function stateChangeHandler1()
						{
							if (xmlHttp.readyState == 4 || xmlHttp.readyState == 'complete')
							{
								var str = xmlHttp.responseText;
								menuData=str;
								//alert(str);
							   	var formid=menuData.split("^");
								var i;
								var dm = new tinymce.ui.DropMenu('mymenu', {container : tinyMCE.activeEditor.getContainer()});
								for(i=1;i<(formid.length);i++)
								{
									var subdata=formid[i];
									dm.add({title : subdata, "myData": i, onclick : function(title){																											
											tinyMCE.execCommand('mceInsertContent',true,this.title);
									return false;
									}});
								}
								dm.destroy();
								dm.showMenu(600, 300);
							}
						}
						
						function xmlHttp_Get(xmlhttp, url)
						{
							xmlhttp.open('GET', url, true);
							xmlhttp.send(null);
						}
						function GetXmlHttpObject(handler)
						{
							var objXmlHttp = null;
							if (is_ie)
							{
								var strObjName = (is_ie5) ? 'Microsoft.XMLHTTP' : 'Msxml2.XMLHTTP';
								try{objXmlHttp = new ActiveXObject(strObjName);
								objXmlHttp.onreadystatechange = handler;
								}
								catch(e)
								{
									alert('IE detected, but object could not be created. Verify that active scripting and activeX controls are enabled');
									return;
								}
							}
							else if (is_opera)
							{
								alert('Opera detected. The page may not behave as expected.');
								return;
							}
							else
							{
								//alert(handler);
								objXmlHttp = new XMLHttpRequest();
								objXmlHttp.onload = handler;
								objXmlHttp.onerror = handler;
							}    
							return objXmlHttp;
						}// JavaScript Document// JavaScript Document// JavaScript Document						
						fill_category_detail(url);
				}
			});    
			
			ed.addButton('mmForm', {
				title : 'Insert MM-Forms',
				cmd : 'mmForm',
				myurl:url,				
				image : url + '/images/mm-form.gif',
				onclick : function(ed, myurl)
				{
						var t = this;						
						////////////Get Data Using Ajax///////////
						var xmlHttp;
						var AdFirst;
						var DIVNAME = "Next";
						var DIVNAME1 = "Type";
						var requestURL;
						var is_ie = (navigator.userAgent.indexOf('MSIE') >= 0) ? 1 : 0; 
						var is_ie5 = (navigator.appVersion.indexOf("MSIE 5.5")!=-1) ? 1 : 0; 
						var is_opera = ((navigator.userAgent.indexOf("Opera 6")!=-1)||(navigator.userAgent.indexOf("Opera/6")!=-1)) ? 1 : 0; 
						var is_netscape = (navigator.userAgent.indexOf('Netscape') >= 0) ? 1 : 0;
						var INCT;
						var SubInd;
						
						function fill_category_detail(url)
						{  
								requestURL =url + "/includes/rpc_1.php";
								var url = requestURL;
								xmlHttp = GetXmlHttpObject(stateChangeHandler1);
								xmlHttp_Get(xmlHttp, url);
						}
						function stateChangeHandler1()
						{
							if (xmlHttp.readyState == 4 || xmlHttp.readyState == 'complete')
							{
								var str = xmlHttp.responseText;
								menuData=str;
								//alert(str);
							   	var formid=menuData.split("^");
								var i;
								var dm = new tinymce.ui.DropMenu('mymenu', {container : tinyMCE.activeEditor.getContainer()});
								for(i=1;i<(formid.length);i++)
								{
									var subdata=formid[i];
									dm.add({title : subdata, "myData": i, onclick : function(title){																											
											tinyMCE.execCommand('mceInsertContent',true,this.title);
									return false;
									}});
								}
								dm.destroy();
								dm.showMenu(600, 300);
							}
						}
						
						function xmlHttp_Get(xmlhttp, url)
						{
							xmlhttp.open('GET', url, true);
							xmlhttp.send(null);
						}
						function GetXmlHttpObject(handler)
						{
							var objXmlHttp = null;
							if (is_ie)
							{
								var strObjName = (is_ie5) ? 'Microsoft.XMLHTTP' : 'Msxml2.XMLHTTP';
								try{objXmlHttp = new ActiveXObject(strObjName);
								objXmlHttp.onreadystatechange = handler;
								}
								catch(e)
								{
									alert('IE detected, but object could not be created. Verify that active scripting and activeX controls are enabled');
									return;
								}
							}
							else if (is_opera)
							{
								alert('Opera detected. The page may not behave as expected.');
								return;
							}
							else
							{
								//alert(handler);
								objXmlHttp = new XMLHttpRequest();
								objXmlHttp.onload = handler;
								objXmlHttp.onerror = handler;
							}    
							return objXmlHttp;
						}// JavaScript Document// JavaScript Document// JavaScript Document						
						fill_category_detail(url);
				}
			});    
  		// Internal functions      
		},
		
		getInfo : function() {
			return {
				longname : 'mmForms',
				author : 'mmForms',
				authorurl : 'http://plugins.motionmill.com/mm-forms/',
				infourl : 'http://plugins.motionmill.com/',
				version : "1.0"
			};
		}
	});
	// Register plugin
	tinymce.PluginManager.add('mmforms', tinymce.plugins.mmforms);
})();