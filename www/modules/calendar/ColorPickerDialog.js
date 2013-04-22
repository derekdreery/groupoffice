GO.calendar.ColorPickerDialog = function(config){

	if(!config)
	{
		config = {};
	}

	this.buildForm();

	var focusFirstField = function(){
		this.formPanel.items.items[0].focus();
	};

	config.layout='fit';
	config.title=GO.calendar.lang.calendarColor;
	config.modal=false;
	config.border=false;
	config.width=420;
	config.autoHeight=true;
	config.resizable=false;
	config.plain=true;
	config.shadow=false,
	config.closeAction='hide';
	config.items=this.formPanel;
	config.focus=focusFirstField.createDelegate(this);
	config.buttons=[{
		text:GO.lang['cmdOk'],
		handler: function()
		{
			this.submitForm(true)
		},
		scope: this
	},{
		text:GO.lang['cmdApply'],
		handler: function()
		{
			this.submitForm(false)
		},
		scope: this
	},{
		text:GO.lang['cmdClose'],
		handler: function()
		{
			this.hide()
		},
		scope: this
	}];

	GO.calendar.ColorPickerDialog.superclass.constructor.call(this,config);

	this.addEvents({'save' : true});
}

Ext.extend(GO.calendar.ColorPickerDialog, Ext.Window, {

	show : function ()
	{
		if(!this.rendered)
			this.render(Ext.getBody());

		this.formPanel.form.reset();
		this.editorGrid.store.load();

		GO.calendar.ColorPickerDialog.superclass.show.call(this);
	},
	submitForm : function(hide)
	{
		this.formPanel.form.submit(
		{
			url : GO.url('calendar/calendar/submitColors'),
			params: {
				griddata: Ext.encode(this.getGridData())
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action)
			{
				this.fireEvent('save');
				this.editorGrid.store.commitChanges();
				if(hide)
				{
					this.hide();
				}
			},
			failure: function(form, action)
			{
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang['strErrorsInForm'];
				}
				else
				{
					error = action.result.feedback;
				}
				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope:this
		});
	},

	getGridData : function(){

		var data = {};

		for (var i = 0; i < this.editorGrid.store.data.items.length;  i++)
		{
			var r = this.editorGrid.store.data.items[i].data;

			data[i]={};

			for(var key in r)
			{
				data[i][key]=r[key];
			}
		}

		return data;
	},
	buildForm : function ()
	{
		var fields ={
		fields:['id','name','color'],
		columns:[	{
			header: GO.calendar.lang.calendar,
			dataIndex: 'name'
		},{
			header: GO.calendar.lang.calendarColor,
			dataIndex: 'color',
			width: 30,
			renderer: function(value,meta,record) {
				return '<div style="background-color: #'+record.data.color+';border: 1px solid #666;width:70px;height:9px;margin-right:4px;float:left;"></div>';
			},
			editor: 
				new GO.form.ColorField({
					value : "EBF1E2",
//					fieldLabel : GO.lang.color,
//					value : GO.calendar.defaultBackground,
//					anchor:'50%',
//					name : 'color',
					colors : [
					'EBF1E2',
					'95C5D3',
					'FFFF99',
					'A68340',
					'82BA80',
					'F0AE67',
					'66FF99',
					'CC0099',
					'CC99FF',
					'996600',
					'999900',
					'FF0000',
					'FF6600',
					'FFFF00',
					'FF9966',
					'FF9900',
					/* Line 1 */
					'FB0467',
					'D52A6F',
					'CC3370',
					'C43B72',
					'BB4474',
					'B34D75',
					'AA5577',
					'A25E79',
					/* Line 2 */
					'FF00CC',
					'D52AB3',
					'CC33AD',
					'C43BA8',
					'BB44A3',
					'B34D9E',
					'AA5599',
					'A25E94',
					/* Line 3 */
					'CC00FF',
					'B32AD5',
					'AD33CC',
					'A83BC4',
					'A344BB',
					'9E4DB3',
					'9955AA',
					'945EA2',
					/* Line 4 */
					'6704FB',
					'6E26D9',
					'7033CC',
					'723BC4',
					'7444BB',
					'754DB3',
					'7755AA',
					'795EA2',
					/* Line 5 */
					'0404FB',
					'2626D9',
					'3333CC',
					'3B3BC4',
					'4444BB',
					'4D4DB3',
					'5555AA',
					'5E5EA2',
					/* Line 6 */
					'0066FF',
					'2A6ED5',
					'3370CC',
					'3B72C4',
					'4474BB',
					'4D75B3',
					'5577AA',
					'5E79A2',
					/* Line 7 */
					'00CCFF',
					'2AB2D5',
					'33ADCC',
					'3BA8C4',
					'44A3BB',
					'4D9EB3',
					'5599AA',
					'5E94A2',
					/* Line 8 */
					'00FFCC',
					'2AD5B2',
					'33CCAD',
					'3BC4A8',
					'44BBA3',
					'4DB39E',
					'55AA99',
					'5EA294',
					/* Line 9 */
					'00FF66',
					'2AD56F',
					'33CC70',
					'3BC472',
					'44BB74',
					'4DB375',
					'55AA77',
					'5EA279',
					/* Line 10 */
					'00FF00', '2AD52A',
					'33CC33',
					'3BC43B',
					'44BB44',
					'4DB34D',
					'55AA55',
					'5EA25E',
					/* Line 11 */
					'66FF00', '6ED52A', '70CC33',
					'72C43B',
					'74BB44',
					'75B34D',
					'77AA55',
					'79A25E',
					/* Line 12 */
					'CCFF00', 'B2D52A', 'ADCC33', 'A8C43B',
					'A3BB44',
					'9EB34D',
					'99AA55',
					'94A25E',
					/* Line 13 */
					'FFCC00', 'D5B32A', 'CCAD33', 'C4A83B',
					'BBA344', 'B39E4D',
					'AA9955',
					'A2945E',
					/* Line 14 */
					'FF6600', 'D56F2A', 'CC7033', 'C4723B',
					'BB7444', 'B3754D', 'AA7755',
					'A2795E',
					/* Line 15 */
					'FB0404', 'D52A2A', 'CC3333', 'C43B3B',
					'BB4444', 'B34D4D', 'AA5555', 'A25E5E',
					/* Line 16 */
					'FFFFFF', '949494', '808080', '6B6B6B',
					'545454', '404040', '292929', '000000']
				})
			}]
		};


		var columnModel =  new Ext.grid.ColumnModel({
			defaults:{
				sortable:true
			},
			columns:fields.columns
		});

		var store = new GO.data.JsonStore({
			url : GO.url('calendar/calendar/loadColors'),
			fields: fields.fields,
			remoteSort: true
		});

		this.editorGrid = new Ext.grid.EditorGridPanel({
			height: 300,
			store: store,
			bbar: new Ext.PagingToolbar({
							cls: 'go-paging-tb',
							store: store,
							pageSize: parseInt(GO.settings['max_rows_list']),
							displayInfo: true,
							displayMsg: GO.lang['displayingItems'],
							emptyMsg: GO.lang['strNoItems']
						}),
			cm: columnModel,
			sm: new Ext.grid.RowSelectionModel(),
			view: new Ext.grid.GridView({
				autoFill: true,
				forceFit: true,
				emptyText: GO.lang['strNoItems']
			}),
			loadMask: true,
			clicksToEdit: 1
		});
		
		this.formPanel = new Ext.FormPanel({
//			cls:'go-form-panel',
			anchor:'100% 100%',		
			defaultType:'textfield',
			autoHeight:true,
			waitMsgTarget:true,
			labelWidth:75,
			items: this.editorGrid
		});
	}
});