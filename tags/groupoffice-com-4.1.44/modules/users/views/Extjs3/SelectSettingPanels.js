/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: SelectSettingPanels.js 12502 2012-11-02 16:26:05Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.users.SelectSettingPanels = Ext.extend(Ext.Panel,{

	initComponent : function(){
		
		this.autoScroll=true;
		this.border=false;
		this.hideLabel=true;
		this.title = GO.users.lang.enabledSettingtabs;
		this.layout='form';
		this.cls='go-form-panel';
		this.labelWidth=50;
		
		GO.users.SelectSettingPanels.superclass.initComponent.call(this);
	}
});			