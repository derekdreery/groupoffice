EventDialog = function(){
	
	var dialog;

	return{
		
		init : function () {
			
			if(!dialog)
			{
				dialog = new Ext.LayoutDialog('event-dialog', {
						modal:true,
						shadow:false,
						resizable:false,
						proxyDrag: true,
						width:600,
						height:250,
						collapsible:false,
						shim:false,
						center: {
							autoScroll:true,
							tabPosition: 'top',
							closeOnTab: true,
							alwaysShowTabs: false
						}
		
					});
					dialog.addKeyListener(27, dialog.hide, dialog);
		
		
					layout = dialog.getLayout();
		
		
					layout.beginUpdate();
				
					
					layout.add('center', new Ext.ContentPanel('event-properties',{
						title: GOlang['strProperties'],
						autoScroll:true					
					}));
					
					this.buildForm();
		
					layout.endUpdate();
			
			}
			
			dialog.show();
		},
		
		
		buildForm : function () {
			
			Ext.QuickTips.init();

		    // turn on validation errors beside the field globally
		    Ext.form.Field.prototype.msgTarget = 'side';
			
			eventForm = new Ext.BasicForm('event-form', {
				waitMsgTarget: 'box-bd'
			});
			
			new Ext.form.TextField({
            name: 'subject',
          	width:300,
            allowBlank:false
        	}).render('subject-field');
        	
        	new Ext.form.TextArea({
            name: 'description',
          	width:300,
            allowBlank:true
        	}).render('description-field');
        	
        	new Ext.form.DateField({
            name: 'startDate',
            width:100,
            format: GOsettings['date_format'],
            allowBlank:false
        	}).render('start-date-field');
        	
        	new Ext.form.ComboBox({            
	            hiddenName:'startHour',
	            store: new Ext.data.SimpleStore({
	                fields: ['value','text'],
	                data: [
	                	['00','00'],
	                	['01','01'],
	                	['02','02'],
	                	['03','03'],
	                	['04','04'],
	                	['05','05'],
	                	['06','06'],
	                	['07','07'],
	                	['08','08'],
	                	['09','09'],
	                	['10','10'],
	                	['11','11'],
	                	['12','12'],
	                	['13','13'],
	                	['14','14'],
	                	['15','15'],
	                	['16','16'],
	                	['17','17'],
	                	['18','18'],
	                	['19','19'],
	                	['20','20'],
	                	['21','21'],
	                	['22','22'],
	                	['23','23']]
	            }),
	            displayField:'text',
	            typeAhead: true,
	            mode: 'local',
	            triggerAction: 'all',
	            selectOnFocus:true,
	            
	            width:40
	        }).render('start-hour-field');
	        
	        new Ext.form.ComboBox({            
	            hiddenName:'startMinute',
	            store: new Ext.data.SimpleStore({
	                fields: ['value','text'],
	                data: [['00','00'],['15','15'],['30','30'],['45','45']]
	            }),
	            displayField:'text',
	            typeAhead: true,
	            mode: 'local',
	            triggerAction: 'all',
	            selectOnFocus:true,
	            width:40
	        }).render('start-minute-field');		
	        
	        
	        new Ext.form.DateField({
            name: 'endDate',
            width:100,
            format: GOsettings['date_format'],
            allowBlank:false
        	}).render('end-date-field');
        	
        	new Ext.form.ComboBox({            
	            hiddenName:'endHour',
	            store: new Ext.data.SimpleStore({
	                fields: ['value','text'],
	                data: [
	                	['00','00'],
	                	['01','01'],
	                	['02','02'],
	                	['03','03'],
	                	['04','04'],
	                	['05','05'],
	                	['06','06'],
	                	['07','07'],
	                	['08','08'],
	                	['09','09'],
	                	['10','10'],
	                	['11','11'],
	                	['12','12'],
	                	['13','13'],
	                	['14','14'],
	                	['15','15'],
	                	['16','16'],
	                	['17','17'],
	                	['18','18'],
	                	['19','19'],
	                	['20','20'],
	                	['21','21'],
	                	['22','22'],
	                	['23','23']]
	            }),
	            displayField:'text',
	            typeAhead: true,
	            mode: 'local',
	            triggerAction: 'all',
	            selectOnFocus:true,
	            
	            width:40
	        }).render('end-hour-field');
	        
	        new Ext.form.ComboBox({            
	            hiddenName:'endMinute',
	            store: new Ext.data.SimpleStore({
	                fields: ['value','text'],
	                data: [['00','00'],['15','15'],['30','30'],['45','45']]
	            }),
	            displayField:'text',
	            typeAhead: true,
	            mode: 'local',
	            triggerAction: 'all',
	            selectOnFocus:true,
	            width:40
	        }).render('end-minute-field');	
	        
	        new Ext.form.Checkbox({
	            boxLabel:'Time is not applicable',
	            name:'all_day',
	            checked:false,
	            width:'auto'
        	}).render('all-day-field');	
		}	
	
	}
}