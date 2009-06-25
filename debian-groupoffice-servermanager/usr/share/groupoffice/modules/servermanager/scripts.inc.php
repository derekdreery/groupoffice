<script type="text/javascript">
GO.servermanager.config={};
<?php 
require('/etc/groupoffice/servermanager.inc.php');

foreach($default_config as $key=>$value)
{
	echo 'GO.servermanager.config["'.$key.'"]="'.$value.'";';
}

?>
</script>