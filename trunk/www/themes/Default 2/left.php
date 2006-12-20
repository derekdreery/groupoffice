<?php
require_once('../../Group-Office.php');
$GO_SECURITY->authenticate();
$charset= isset($charset) ? $charset : 'UTF-8';
$htmldirection= isset($htmldirection) ? $htmldirection : 'ltr';
header('Content-Type: text/html; charset='.$charset);

$getadmin = isset( $_GET['admin'] ) ? $_GET['admin'] : false;
$adminmodules = false;
?>
<html>
<head>
  <link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
  <?php require($GO_CONFIG->control_path.'fixpng.inc'); ?>
</head>
<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" class="NavBar">
  <tr>
    <td valign="top" align="center" nowrap>
		<br />
			<?php			
			$modules = $GO_MODULES->get_modules_with_locations($getadmin);
			while ( $module = array_shift( $modules ) )
			{
			  if ( $adminmodules ||
			       ( $GO_SECURITY->has_permission( $GO_SECURITY->user_id, $module['acl_read'] ) ||
			         $GO_SECURITY->has_permission( $GO_SECURITY->user_id, $module['acl_write'] ) ||
			         $GO_SECURITY->has_admin_permission( $GO_SECURITY->user_id ) ) ) {
			    $GO_THEME->images[$module['id']] = isset($GO_THEME->images[$module['id']]) ? $GO_THEME->images[$module['id']] : $GO_THEME->images['unknown'];
			    
			    //require language file to obtain module name in the right language
			    $language_file = $GO_LANGUAGE->get_language_file($module['id']);
			    if(file_exists($language_file))
			    {
			    	require_once($language_file);
			    }
			    $lang_var = isset($lang_modules[$module['id']]) ? $lang_modules[$module['id']] : $module['id'];
				?>
				    	<a class="ModuleIcons" target="main" id="<?php echo $module['id']; ?>" href="<?php echo $module['url']; ?>">
				      <img src="<?php echo $GO_THEME->images[$module['id']]; ?>" border="0" width="32" height="32" />
				      <br />
				      <?php echo $lang_var; ?>
				      <br /><br />
				    </a>
				<?php
			  }  
			}
			if ( $GO_SECURITY->has_admin_permission( $GO_SECURITY->user_id ) ) {
			?>
				    <a class="ModuleIcons" target="left" href="<?php echo $GO_THEME->theme_url.'left.php?admin='.!$getadmin; ?>">
				    	<?php
				    	if($getadmin)
				    	{
				    		?>
				      	<img src="<?php echo $GO_THEME->images['close']; ?>" border="0" width="32" height="32" />
				      	<br />
				      	<?php echo $cmdClose; 
				    	}else
				    	{
				    		?>
				      	<img src="<?php echo $GO_THEME->images['admin']; ?>" border="0" width="32" height="32" />
				      	<br />
				      	<?php echo $menu_admin; 
				    	}
				    	?>	
				    	<br /><br />      	
				    </a>
						
			<?php
			}
			?>
			 </td>
  </tr>
</table>
</body>
</html>
