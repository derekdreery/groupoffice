
<!--"CONVERTED_APPLET"-->
<!-- HTML CONVERTER -->
<SCRIPT LANGUAGE="JavaScript"><!--
    var _info = navigator.userAgent; 
    var _ns = false; 
    var _ns6 = false;
    var _ie = (_info.indexOf("MSIE") > 0 && _info.indexOf("Win") > 0 && _info.indexOf("Windows 3.1") < 0);
//--></SCRIPT>
    <COMMENT>
        <SCRIPT LANGUAGE="JavaScript1.1"><!--
        var _ns = (navigator.appName.indexOf("Netscape") >= 0 && ((_info.indexOf("Win") > 0 && _info.indexOf("Win16") < 0 && java.lang.System.getProperty("os.version").indexOf("3.5") < 0) || (_info.indexOf("Sun") > 0) || (_info.indexOf("Linux") > 0) || (_info.indexOf("AIX") > 0) || (_info.indexOf("OS/2") > 0) || (_info.indexOf("IRIX") > 0)));
        var _ns6 = ((_ns == true) && (_info.indexOf("Mozilla/5") >= 0));
//--></SCRIPT>
    </COMMENT>

<SCRIPT LANGUAGE="JavaScript"><!--
    if (_ie == true) document.writeln('<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = "<?php echo $jupload_width; ?>" HEIGHT = "<?php echo $jupload_height; ?>"  codebase="http://java.sun.com/update/1.4.2/jinstall-1_4-windows-i586.cab#Version=1,4,0,0"><NOEMBED><XMP>');
    else if (_ns == true && _ns6 == false) document.writeln('<EMBED \
	    type="application/x-java-applet;version=1.4" \
            CODE = "jupload.JUploadApplet" \
            ARCHIVE = "<?php echo $GO_CONFIG->control_url; ?>JUpload/jupload.jar" \
            WIDTH = "640" \
            HEIGHT = "300" \
            postURL ="<?php echo $post_url; ?>" \
	    scriptable=false \
	    pluginspage="http://java.sun.com/products/plugin/index.html#download"><NOEMBED><XMP>');
//--></SCRIPT>
<APPLET  CODE = "jupload.JUploadApplet" ARCHIVE = "<?php echo $GO_CONFIG->control_url; ?>JUpload/jupload.jar" WIDTH = "<?php echo $jupload_width; ?>" HEIGHT = "<?php echo $jupload_height; ?>"></XMP>
    <PARAM NAME = CODE VALUE = "jupload.JUploadApplet" >
    <PARAM NAME = ARCHIVE VALUE = "<?php echo $GO_CONFIG->control_url; ?>JUpload/jupload.jar" >
    <PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">
    <PARAM NAME="scriptable" VALUE="false">
    <PARAM NAME="postURL" VALUE ="<?php echo $jupload_post_url; ?>">

Java 1.4 or higher plugin required.
</APPLET>
</NOEMBED>
</EMBED>
</OBJECT>

<!--
<APPLET CODE = "jupload.JUploadApplet" ARCHIVE = "jupload.jar" WIDTH = "640" HEIGHT = "300">
<PARAM NAME = "postURL" VALUE ="<?php echo $post_url; ?>">
Java 1.4 or higher plugin required.

</APPLET>
-->
<!--"END_CONVERTED_APPLET"-->
