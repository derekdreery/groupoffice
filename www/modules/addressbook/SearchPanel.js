GO.addressbook.SearchPanel = function(config)
{
	if(!config){
		config = {};
	}
	
	config.layout='table';
	config.split=false;
 	config.height=40;
 	config.forceLayout=true;
	config.baseCls='x-plain';

 	config.keys= [{
            key: Ext.EventObject.ENTER,
            fn: function(){
            	this.selectedLetter='';
							this.alphabetView.clearSelections();
							this.fireQueryEvent();
            },
            scope:this
        }];

  this.alphabetArray = GO.addressbook.lang['alphabet'].split(",");
  
 	var alphabetStoreData = new Array();
 	alphabetStoreData.push({value: '[0-9]'});
 	
 	for(var i = 0;i<this.alphabetArray.length;i++)
 	{
 	  alphabetStoreData.push({value: this.alphabetArray[i]});
 	}    
    
  var tpl = new Ext.XTemplate(
		'<tpl for=".">',
    '<span class="letter" onclick="">{value}</span>',
    '</tpl>'
	);	    
  
  this.selectedLetter = 0;
 	this.alphabetView = new Ext.DataView({
		height:30,
    store: new Ext.data.JsonStore({
	    fields: ['value'],
	   	data : alphabetStoreData
    }),
    tpl: tpl,
    autoHeight:true,
    singleSelect: true,
    cls: 'alphabet-view',
    overClass:'alphabet-view-over',
    selectedClass: 'alphabet-view-selected',
    itemSelector:'span.letter'	   	  
 	});
 	
 	this.alphabetView.on('selectionchange',
 		function(dataview, arraySelections)
 		{
 			if(arraySelections[0])
 			{		   				
 				this.selectedLetter = arraySelections[0].innerHTML;
 				this.queryField.setValue("");
 				this.fireQueryEvent();
 			}
 		},
 	this);
 	
 	this.alphabetView.on('containerclick', 
 	function(dataview, e)
	{
		return false;
	},
 	this);
 	
 	
 	
 	this.queryField = new Ext.form.TextField({
    name: 'query',
    width: 200,
		emptyText:GO.lang.strSearch+ ' '+GO.addressbook.lang['cmdFormSearchFourth']
  });
 	
 	config.defaults={border: false, cls:'ab-search-form-panel', baseCls:'x-plain', forceLayout:true};
 	config.items=[{
 						items: this.alphabetView
 					},{
		        items: this.queryField
			    }, 	
 					{
		        items: new Ext.Button({
							handler: function()
							{					
								this.selectedLetter='';
								this.alphabetView.clearSelections();
								this.fireQueryEvent();									
							},
							text: GO.lang.strSearch,
							scope: this				          
		        })
			    },{
		        items: new Ext.Button({
							handler: function()
							{					
								if(!this.advancedSearchWindow)
								{
									this.advancedSearchWindow = GO.addressbook.advancedSearchWindow = new GO.addressbook.AdvancedSearchWindow();
									this.advancedSearchWindow.on('ok', function(win){

										//See bug report:
										//https://sourceforge.net/tracker/?func=detail&aid=2871777&group_id=76359&atid=547651
										win.grid.stopEditing();

										this.fireEvent('queryChange', {advancedQuery:GO.addressbook.searchQueryPanel.queryField.getValue()});
										//this.fireEvent('queryChange', {advancedQuery:Ext.encode(win.getGridData())});
									}, this)
								}
								var type = this.ab.tabPanel.getActiveTab().id=='ab-contacts-grid' ? 'contacts' : 'companies';
								this.advancedSearchWindow.show(type);
							},
							text: GO.addressbook.lang.advancedSearch,
							scope: this				          
		        })
			    },{
			       items: new Ext.Button({
								handler: function()
								{				 	 
									this.selectedLetter='';
									this.alphabetView.clearSelections();
									this.queryField.setValue("");
									
									this.fireQueryEvent();
								},
								text: GO.lang.cmdReset,
								scope: this				          
			        })
			    }];
	
	GO.addressbook.SearchPanel.superclass.constructor.call(this, config);
	
	this.addEvents({queryChange : true});	
}

Ext.extend(GO.addressbook.SearchPanel, Ext.Panel, {
	selectedLetter : '',
	
	fireQueryEvent : function(){
		var params = {
			clicked_letter : this.selectedLetter,
			query : this.queryField.getValue(),
			advancedQuery : ''
		};
		
		this.fireEvent('queryChange', params);
	}
	
});