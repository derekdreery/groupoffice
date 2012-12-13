<?php
if(!empty($GLOBALS['GO_CONFIG']->serverclient_domains))
{	
	$domains = explode(',', $GLOBALS['GO_CONFIG']->serverclient_domains);
	$GO_SCRIPTS_JS .= 'Ext.namespace("GO.serverclient");GO.serverclient.domains=["'.implode('","', $domains).'"];';
}
?>