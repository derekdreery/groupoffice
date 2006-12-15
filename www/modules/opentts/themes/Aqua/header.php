<?php
function OpenTable(){
	echo "<table border=1 width=100%><tr><td width=100%>";
}

function CloseTable(){
	echo "</td></tr></table>";
}

function OpenTable2(){
        echo "<table border=1 width=200px><tr><td>";
}

function CloseTable2(){
        echo "</td></tr></table>";
}
function head(){
	global $charset,$rtl_dir;
/*
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
        echo "<html>\n";
        echo "<head>\n";
        echo "<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset='$charset'\">";
        echo "</head>";
        echo "<body dir=\"$rtl_dir\">";
*/
}

#head();
?>


