<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$title = GO::config()->get_setting('mediawiki_title');
if (empty($title)) $title='Mediawiki';

/*
if(!$mode){
	$mode='grid';
}
$GO_SCRIPTS_JS .= 'Ext.namespace("GO.mediawiki");Ext.namespace("GO.mediawiki.settings");'.
		'GO.mediawiki.settings.externalUrl="'.GO::config()->get_setting('mediawiki_external_url').'";'.
		'GO.mediawiki.settings.title="'.$title.'";';
 */

?>
<script type="text/javascript">
	Ext.namespace("GO.mediawiki");Ext.namespace("GO.mediawiki.settings");
	GO.mediawiki.settings.externalUrl='<?php echo GO::config()->get_setting('mediawiki_external_url'); ?>';
	GO.mediawiki.settings.title='<?php echo $title; ?>';
</script>