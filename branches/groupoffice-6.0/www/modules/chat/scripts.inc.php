<?php


/**
 * Comment here for explanation of the options.
 *
 * Create a new XMPP Object with the required params
 *
 * @param string $jabberHost Jabber Server Host
 * @param string $boshUri    Full URI to the http-bind
 * @param string $resource   Resource identifier
 * @param bool   $useSsl     Use SSL (not working yet, TODO)
 * @param bool   $debug      Enable debug
 */


$sessionInfo = \GO\Chat\ChatModule::getPrebindInfo();


if($sessionInfo){

$GO_SCRIPTS_JS .= '
	
Ext.DomHelper.append(Ext.getBody(),
		{
			tag: "div",
			id: "conversejs"
		},true);
				
			require(["converse"], function (converse) {
		converse.initialize({
				allow_otr: true,
				auto_list_rooms: false,
				auto_subscribe: false,
				bosh_service_url: "' . \GO\Chat\ChatModule::getBoshUri() . '", // Please use this connection manager only for testing purposes
				debug: false ,
				hide_muc_server: false,
				i18n: locales["en"], // Refer to ./locale/locales.js to see which locales are supported
				prebind: true,
				show_controlbox_by_default: false,
				xhr_user_search: false,
				jid:"'.$sessionInfo['jid'].'",
				sid:"'.$sessionInfo['sid'].'",
				rid:"'.$sessionInfo['rid'].'"
		});
});


';

}
