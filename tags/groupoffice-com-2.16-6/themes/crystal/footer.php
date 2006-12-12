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
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>">
 <link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
 <script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/pngfix.js"></script>
</head>
<body style="padding:0px;margin:0px;" dir="<?php echo $htmldirection; ?>">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" class="FooterBar">
  <tr>
    <td>
      <table border="0">
			<tr>
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
				  <td class="ModuleIcons" align="center" valign="top" nowrap>
				    <a class="FooterBar" target="main" id="<?php echo $module['id']; ?>" href="<?php echo $module['url']; ?>">
				      <img src="<?php echo $GO_THEME->images[$module['id']]; ?>" border="0" width="32" height="32" />
				      <br />
				      <?php echo $lang_var; ?>
				    </a>
				  </td>
				<?php
			  }  
			}
			if ( $GO_SECURITY->has_admin_permission( $GO_SECURITY->user_id ) ) {
			?>
				  <td class="ModuleIcons" align="center" valign="top" nowrap>
				    <a class="FooterBar" target="footer" href="<?php echo $GO_THEME->theme_url.'footer.php?admin='.!$getadmin; ?>">
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
				    </a>
				  </td>
			<?php
			}
			?>
				</tr>
      </table>
    </td>
    <td align="right">
      <a target="main" href="<?php echo $GO_CONFIG->host; ?>about.php" title="<?php echo $menu_about; ?>">
				<img src="<?php echo $GO_THEME->images['go_header']; ?>" border="0" style="margin-right:20px;" />
      </a>
    </td>
  </tr>
</table>
</body>
</html>
