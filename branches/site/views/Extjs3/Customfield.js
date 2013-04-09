GO.moduleManager.onModuleReady('customfields', function(){
	
	GO.customfields.nonGridTypes.push('sitefile');
	GO.customfields.dataTypes.GO_Site_Customfieldtype_Sitefile={
		label : GO.site.lang.siteFile,
		getFormField : function(customfield, config){
			return {
				xtype: 'siteselectfile',
       	fieldLabel: customfield.name,
        name:customfield.dataname,
        anchor:'-20'
			}
		}
	}
	
//	GO.customfields.nonGridTypes.push('sitemultifile');
//	GO.customfields.dataTypes.GO_Site_Customfieldtype_SiteMultifile={
//		label : GO.site.lang.siteMultiFile,
//		getFormField : function(customfield, config){
//			return {
//				xtype: 'selectfile',
//       	fieldLabel: customfield.name,
//        name:customfield.dataname,
//        anchor:'-20'
//			}
//		}
//	}

}, this);