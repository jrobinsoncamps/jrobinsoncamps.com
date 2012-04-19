<html>
	<head>
		<title>Ajax File Uploader Plugin For Jquery</title>
<link href="css/ajaxfileupload.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/ajaxfileupload.js"></script>
	<script type="text/javascript">
	

	function ajaxFileUpload()
	{
		types = document.getElementById('ufiletypes').value;
		filesize = document.getElementById('maxfilesize').value;
		
		ftupload = document.getElementById('fileToUpload').value;
		
		var arr = ftupload.split(".");
		var ext = arr[arr.length - 1];
		
		var found = types.indexOf("."+ext);
		if(found <= -1 && types.length != 0 ) {
			alert('only file types ('+ types + ") can be uploaded!");
			return false;
		}	
		
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload
		(
			{
				url:'doajaxfileupload.php?types='+types+"&filesize="+filesize,
				secureuri:false,
				fileElementId:'fileToUpload',
				dataType: 'json',
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
						}else
						{
							//alert(data.msg)
							var doc = parent.document.getElementById("uploaded-file-"+document.getElementById('parent_file_field').value);
							var tumb_image = parent.document.getElementById("thumb_image-"+document.getElementById('parent_file_field').value);
							alert (document.getElementById('parent_file_field').value);
							doc.value = data.filename;
							// alert("http://" + window.parent.location.hostname + "/wp-content/plugins/mm-forms/upload/temp/"+ data.filename);
							tumb_image.src = "http://" + window.parent.location.hostname + "/wp-content/plugins/mm-forms-community/upload/temp/"+ data.filename;
							tumb_image.width = "200";
							
						}
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)
		
		return false;

	}
	</script>	
	</head>

	<body style="margin:0">
    <div id="content">
	
		<img id="loading" src="../images/loading.gif" style="display:none;">	
      <form name="form" action="" method="POST" enctype="multipart/form-data">
	  	<input type="hidden" name="ufiletypes" id="ufiletypes" value="<?php echo $_GET['ufiletypes']?>"/>
		<input type="hidden" name="maxfilesize" id="maxfilesize" value="<?php echo $_GET['maxfilesize']?>"/>
		<input type="hidden" name="parent_file_field" id="parent_file_field" value="<?php echo $_GET['fieldname']?>"/>
		
   		<input id="fileToUpload" type="file" size="28" name="fileToUpload" class="input" onChange="return ajaxFileUpload();">
	  </form>    	
    </div>
    

	</body>
</html>
