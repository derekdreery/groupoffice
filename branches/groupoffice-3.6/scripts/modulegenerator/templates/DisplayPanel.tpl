/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: DisplayPanel.tpl 2276 2008-07-04 12:22:20Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.{module}.{friendly_single_ucfirst}Panel = Ext.extend(GO.DisplayPanel,{

	link_type : {link_type},
		
	loadParams : {task: '{friendly_single}_with_items'},
	
	idParam : '{friendly_single}_id',
	
	loadUrl : GO.settings.modules.{module}.url+'json.php',
	
	editHandler : function(){
		GO.{module}.{friendly_single}Dialog.show({ {friendly_single}_id: this.link_id});
		this.addSaveHandler(GO.{module}.{friendly_single}Dialog);
	},
	
	initComponent : function(){
	
		this.template =
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">Information</td>'+
					'</tr>'+

				{DISPLAYFIELDS}
									
				'</table>';																		
				<gotpl if="$link_type &gt; 0">
				this.template += GO.linksTemplate;
												
				if(GO.customfields)
				{
					this.template +=GO.customfields.displayPanelTemplate;
				}</gotpl>
		
		<gotpl if="$files">		
		if(GO.files)
		{
			Ext.apply(this.templateConfig, GO.files.filesTemplateConfig);
			this.template += GO.files.filesTemplate;
		}
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);
		</gotpl>
		
		this.xtemplate = new Ext.XTemplate(this.template, this.templateConfig);
		
		GO.{module}.{friendly_single_ucfirst}Panel.superclass.initComponent.call(this);
	}
	
});			