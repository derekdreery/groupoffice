<script language="javascript" type="text/javascript">
function fixPNG()
{
	for(var i=0; i<document.images.length; i++)
	{
		if(document.images[i].src)
		{
			var imgName = document.images[i].src.toUpperCase()
			if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
			{
				document.images[i].runtimeStyle.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + document.images[i].src + "', sizingMethod='image');";
				document.images[i].src='<?php echo $GO_THEME->images['blank']; ?>';
			}
		}
	}
}

if(BrowserDetect.browser=='Explorer' && BrowserDetect.version>5 && BrowserDetect.version<7)
{
	window.attachEvent("onload", fixPNG);
}
</script>
