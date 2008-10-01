<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id: default_scripts.inc.php 2952 2008-09-03 09:47:49Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

$suffix = '?'.$GO_CONFIG->mtime;

?>
<script	src="<?php echo $GO_CONFIG->host; ?>ext/adapter/ext/ext-base.js<?php echo $suffix; ?>" type="text/javascript"></script>
<?php
if($GO_CONFIG->debug)
{
?>
<script	src="<?php echo $GO_CONFIG->host; ?>ext/ext-all-debug.js<?php echo $suffix; ?>"	type="text/javascript"></script>
<?php
}else
{
?>
<script	src="<?php echo $GO_CONFIG->host; ?>ext/ext-all.js<?php echo $suffix; ?>"	type="text/javascript"></script>
<?php
}
?>
<script	src="<?php echo $GO_CONFIG->host; ?>javascript/namespaces.js<?php echo $suffix; ?>"	type="text/javascript"></script>

<script type="text/javascript">
var BaseHref = '<?php echo $GO_CONFIG->host; ?>';
Ext.BLANK_IMAGE_URL = '<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/s.gif';

<?php
$settings = $GO_CONFIG->get_client_settings();	
?>
GO.settings = Ext.decode('<?php echo addslashes(json_encode($settings)); ?>');

//An object of functions that open a particular link.
//the index is the link type and the function gets the id as a parameter
GO.linkHandlers={};

GO.newMenuItems=[];

<?php

if(isset($_REQUEST['after_login_url']))
{
	echo 'GO.afterLoginUrl="'.smart_stripslashes($_REQUEST['after_login_url']).'";';
}
?>

</script>
<?php
if(!$GO_CONFIG->debug)
{

	if(!is_dir($GO_CONFIG->local_path.'cache'))
	{
		mkdir($GO_CONFIG->local_path.'cache', 0755, true);
	}

	$scripts=array(
	$GO_CONFIG->root_path.'language/common/en.js',
	$GO_MODULES->modules['users']['path'].'language/en.js',
	);

	if($GO_LANGUAGE->language!='en')
	{
		if(file_exists($GO_CONFIG->root_path.'language/common/'.$GO_LANGUAGE->language.'.js'))
		{
			$scripts[] =$GO_CONFIG->root_path.'language/common/'.$GO_LANGUAGE->language.'.js';
		}

		if(file_exists($GO_CONFIG->root_path.'ext/build/locale/ext-lang-'.$GO_LANGUAGE->language.'.js'))
		{
			$scripts[]=$GO_CONFIG->root_path.'ext/build/locale/ext-lang-'.$GO_LANGUAGE->language.'.js';
		}

		if(file_exists($GO_MODULES->modules['users']['path'].'language/'.$GO_LANGUAGE->language.'.js'))
		{
			$scripts[]=$GO_MODULES->modules['users']['path'].'language/'.$GO_LANGUAGE->language.'.js';
		}
	}

	
	$scripts[]=$GO_CONFIG->root_path.'javascript/go-all-min.js';
	
	
	$file = 'base-'.md5($GO_LANGUAGE->language.$GO_CONFIG->mtime).'.js';
	$path = $GO_CONFIG->local_path.'cache/'.$file;
	$url = $GO_CONFIG->local_url.'cache/'.$file;
	
	if(!file_exists($path))
	{
		//cleanup old cache
		$fs = new filesystem();
		$files = $fs->get_files_sorted($GO_CONFIG->local_path.'cache');
		while($file=array_shift($files))
		{
			if(substr($file['name'],0, 7)=='base-'.$GO_LANGUAGE->language)
			{
				unlink($file['path']);
			}
		}
		
		echo "<!-- regenerated script -->\n";		
		foreach($scripts as $script)
		{
			file_put_contents($path,"\n\n/*".$script."*/\n\n".file_get_contents($script),FILE_APPEND);
		}
	}
	echo '<script src="'.$url.'" type="text/javascript"></script>';
	
	$scripts=array();	
	
	if($GO_SECURITY->logged_in())
	{
		$modules=array();
		foreach($GO_MODULES->modules as $module)
		{
			if($module['read_permission'])
			{
				if(file_exists($module['path'].'language/en.js'))
				{
					$scripts[]=$module['path'].'language/en.js';
				}
	
				if($GO_LANGUAGE->language!='en' && file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js'))
				{
					$scripts[]=$module['path'].'language/'.$GO_LANGUAGE->language.'.js';
				}
	
				if(file_exists($module['path'].'all-module-scripts-min.js'))
				{
					$scripts[]=$module['path'].'all-module-scripts-min.js';
				}
	
				$modules[]=$module['id'];
			}
		}
	
		
		$file = $GO_SECURITY->user_id.'-'.md5($GO_CONFIG->mtime.':'.$GO_LANGUAGE->language.':'.implode(':', $modules)).'.js';
		$path = $GO_CONFIG->local_path.'cache/'.$file;
		$url = $GO_CONFIG->local_url.'cache/'.$file;
		
		if(!file_exists($path))
		{
			//cleanup old cache
			$fs = new filesystem();
			$files = $fs->get_files_sorted($GO_CONFIG->local_path.'cache');
			while($file=array_shift($files))
			{
				if(substr($file['name'],0, 1)==$GO_SECURITY->user_id)
				{
					unlink($file['path']);
				}
			}
			
			echo "<!-- regenerated script -->\n";		
			foreach($scripts as $script)
			{
				file_put_contents($path,"\n\n/*".$script."*/\n\n".file_get_contents($script),FILE_APPEND);
			}
		}	
	
		echo '<script src="'.$url.'" type="text/javascript"></script>';

		foreach($GO_MODULES->modules as $module)
		{
			if($module['read_permission'])
			{
				if(file_exists($module['path'].'scripts.inc.php'))
				{
					require($module['path'].'scripts.inc.php');
				}
			}
		}
	}
}else
{
	?>
	<script	src="<?php echo $GO_CONFIG->host; ?>language/common/en.js<?php echo $suffix; ?>" type="text/javascript"></script>
	<script	src="<?php echo $GO_MODULES->modules['users']['url'].'language/en.js'.$suffix; ?>" type="text/javascript"></script>
	<?php

	if($GO_LANGUAGE->language!='en')
	{
		if(file_exists($GO_CONFIG->root_path.'language/common/'.$GO_LANGUAGE->language.'.js'))
		{
			echo '<script type="text/javascript" src="'.$GO_CONFIG->host.'language/common/'.$GO_LANGUAGE->language.'.js'.$suffix.'"></script>';
			echo "\n";
		}

		if(file_exists($GO_CONFIG->root_path.'ext/build/locale/ext-lang-'.$GO_LANGUAGE->language.'.js'))
		{
			echo '<script type="text/javascript" src="'.$GO_CONFIG->host.'ext/build/locale/ext-lang-'.$GO_LANGUAGE->language.'.js'.$suffix.'"></script>';
			echo "\n";
		}

		if(file_exists($GO_MODULES->modules['users']['path'].'language/'.$GO_LANGUAGE->language.'.js'))
		{
			echo '<script type="text/javascript" src="'.$GO_MODULES->modules['users']['url'].'language/'.$GO_LANGUAGE->language.'.js'.$suffix.'"></script>';
			echo "\n";
		}
	}
	

	$data = file_get_contents($GO_CONFIG->root_path.'/javascript/scripts.txt');
	$lines = explode("\n", $data);
	foreach($lines as $line)
	{
		if(!empty($line))
		{
			echo '<script type="text/javascript" src="'.$GO_CONFIG->host.$line.$suffix.'"></script>';
			echo "\n";
		}
	}


	foreach($GO_MODULES->modules as $module)
	{
		if($module['read_permission'])
		{
			if(file_exists($module['path'].'language/en.js'))
			{
				echo '<script type="text/javascript" src="'.$module['url'].'language/en.js"></script>';
				echo "\n";
			}

			if($GO_LANGUAGE->language!='en' && file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js'))
			{
				echo '<script type="text/javascript" src="'.$module['url'].'language/'.$GO_LANGUAGE->language.'.js"></script>';
				echo "\n";
			}


			if(file_exists($module['path'].'scripts.txt') && $GO_CONFIG->debug)
			{
				$data = file_get_contents($module['path'].'scripts.txt');
				$lines = explode("\n", $data);
				foreach($lines as $line)
				{
					if(!empty($line))
					{
						echo '<script type="text/javascript" src="'.$GO_CONFIG->host.$line.'"></script>';
						echo "\n";
					}
				}
			}else if(file_exists($module['path'].'all-module-scripts-min.js'))
			{
				echo '<script type="text/javascript" src="'.$module['url'].'all-module-scripts-min.js"></script>';
				echo "\n";
			}
				
			if(file_exists($module['path'].'scripts.inc.php'))
			{
				require($module['path'].'scripts.inc.php');
			}
		}
	}
}
?>
<script type="text/javascript">
Ext.state.Manager.setProvider(new GO.state.HttpProvider({url: BaseHref+'state.php'}));
</script>
<?php

if(file_exists($GO_THEME->theme_path.'MainLayout.js'))
{
	echo '<script src="'.$GO_THEME->theme_url.'MainLayout.js'.$suffix.'" type="text/javascript"></script>';
	echo "\n";
	
}
?>