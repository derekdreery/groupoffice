<?php

class Configure{
	function get_themes(){
	global $name;
	$themes= null;
	if ($handle = opendir("modules/$name/themes")) {
	   echo "Themes:\n";

	   /* This is the correct way to loop over the directory. */
	   while (false !== ($file = readdir($handle))) {
        	if (is_dir("modules/$name/themes/$file")  and $file!="." and  $file !=".." and $file!="CVS"){
	               $themes[]="$file";
        	}
	   }
	   closedir($handle);
	}
	return $themes;
	}
	function change_theme($theme_selected){
	global $name;
	$hlpdsk_theme="$theme_selected";
	$file=file("modules/$name/configure.php");
	$fp1=fopen ("modules/$name/configure.php","w");
	foreach ($file as $line){
	        if (strstr($line,"\$hlpdsk_theme")){
        	        fwrite($fp1,"\$hlpdsk_theme=\"$theme_selected\";\n");
	        }else{
        	        fwrite($fp1,$line);
	        }
	}
	fclose($fp1);
	}


}

?>
