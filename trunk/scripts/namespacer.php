<?php


//First I refactored all classes that ended on "_Function", "_Interface", "_Abstract", "_Switch" and
//"_Array", "_List". Otherwise this script would create illegal class names as
//class Interface is not allowed.

//After this script I still had to prefix some manual usages of php classes.
//for exmple PDO had to become \PDO


//find all PHP files except updates.php and updates.inc.php because we shouldn't touch them

$cmd = 'find . -type f \( -iname "*.php" ! -iname "updates*" \);';
exec($cmd, $scripts, $return_var);

//return var should be 0 otherwise something went wrong
if($return_var!=0)
	exit("Find command did not run successfully.\n");

foreach($scripts as $script){
	
	//skip old files. We don't use .inc.php anymore in the new framework
	if(substr($script,-14)=='.class.inc.php' || in_array(basename($script),array('namespacer.php','action.php','json.php')))
		continue;
	
	
	
	//get the contents
	$content = $oldContent = file_get_contents($script);
	
	//Our main global static function GO::function() is easiest to identify like this
	$content = str_replace('GO::', '\\GO::', $content);
	
	//All GO classes are build up like GO_Module_SomeClass so we can match them with
	//a regular expression.
	$regex = '/[^A-Za-z0-9_-](GO(_[A-Za-z0-9]+)+)\b/';
	
	$classes = array();
	
	if(preg_match_all($regex, $content, $matches))
	{
		
		
		//loop through the matched class names and store the old classname as key
		//and the new classname as value
		foreach($matches[1] as $className){
			
			//skip all uppercase classnames. They are old eg. GO_USERS, GO_LINKS
			if($className!=strtoupper($className)){	
				if(!in_array($className, $classes)){
					$classes[$className]='\\'.str_replace('_','\\', $className);
				}
			}
		}
		
		//replace all class names
		foreach($classes as $oldClassName=>$newClassName){
			$content = str_replace($oldClassName, $newClassName, $content);
		}
		
		//now we have a problem with the class declarations.		
		//we only have one class per file!
		foreach($classes as $oldClassName=>$newClassName){
			$classDeclarationRegex = '/(class|interface)\s('.preg_quote($newClassName,'/').')/';
			
			if(preg_match($classDeclarationRegex,$content, $classDeclarationMatches,PREG_OFFSET_CAPTURE)){
				
				echo "Found ".$newClassName."\n";
				
				//strip last part of the class name to become the namespace.
				//eg. class; \GO\Email\Model\ImapMessageAttachment will have namespace:
				//GO\Email\Model
				$namespace = trim($newClassName,'\\');
				$lastBackSlashPos = strrpos($namespace,'\\');
				$namespace = substr($namespace,0, $lastBackSlashPos);
				

				//find place in the file to enter the "namespace GO\Email\Model;" declaration.
				//we can do this above the line with declaration "class ImapMessageAttachment"				
				$offset = $classDeclarationMatches[0][1];
				
//				echo substr($content,0,$offset)."\n";
				
				$lastLineBreakPos = strrpos(substr($content,0,$offset), "\n");
//				var_dump($classDeclarationMatches);
				
//				echo $offset.':'.$lastLineBreakPos.':'.strlen($content)."\n";
				
				$declaration = "\n\nnamespace ".$namespace.";\n\n";
				
				$firstPart = substr($content,0,$lastLineBreakPos);
				$lastPart = substr($content, $lastLineBreakPos);				
				$content = $firstPart.$declaration.$lastPart;
				
				//now we must remove the namespace from class usages in this file.
				$content = preg_replace('/([^"\'])\\\\'.preg_quote($namespace,'/').'\\\\/', "$1", $content);
				
//				echo $content; 
//				exit();
			}
		}
		

	}
	
	//some doubles could have been made
	$content = str_replace('\\\\GO', '\\GO', $content);
	
	//if the contents were modified then write them to the file.
	if($oldContent != $content){
		echo "\nReplacing $script\n";
		file_put_contents($script, $content);
	}
	
}

