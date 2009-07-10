<?php
if(!empty($GO_CONFIG->serverclient_domains))
{
	
	$domains = explode(',', $GO_CONFIG->serverclient_domains);
	echo '<script type="text/javascript">
	Ext.namespace("GO.serverclient");
	GO.serverclient.domains=["'.implode('","', $domains).'"];
	</script>';
}
?>