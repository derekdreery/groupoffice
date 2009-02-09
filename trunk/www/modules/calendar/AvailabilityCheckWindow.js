GO.calendar.AvailabilityWindow = function(config) {
	config = config || {};

	var tpl = new Ext.XTemplate(
			'<div id="availability_date"></div>',
			'<table class="availability">',
			'<tr><td></td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("0", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("1", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("2", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("3", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("4", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("5", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("6", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("7", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("8", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("9", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("10", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("11", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("12", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("13", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("14", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("15", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("16", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("17", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("18", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("19", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("20", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("21", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("22", "G").format(GO.settings.time_format)
					+ '</td>',
			'<td colspan="4" class="availability_time">'
					+ Date.parseDate("23", "G").format(GO.settings.time_format)
					+ '</td>',

			'<tpl for=".">',
			'<tr>',
			'<td>{name}</td>',
			'<tpl if="this.hasFreeBusy(freebusy)">',
			'<tpl for="freebusy">',
			'<td id="time{time}"class="time {[values.busy == 1 ? "busy" : "free"]}"></td>',
			'</tpl>', '</tpl>', '<tpl if="!this.hasFreeBusy(freebusy)">',
			'<td colspan="96">' + GO.calendar.lang.noInformationAvailable
					+ '</td>', '</tpl>', '</tr>', '</tpl>', '</table>', {
				hasFreeBusy : function(freebusy) {
					return freebusy.length > 0;
				}
			});

	this.dataView = new Ext.DataView({
				store : new Ext.data.JsonStore({
							url : GO.settings.modules.calendar.url + 'json.php',
							root : 'participants',
							fields : ['name', 'email', 'freebusy'],
							baseParams : {
								task : 'availability',
								emails:'',
								event_id:0,
								date: ''
							}
						}),
				tpl : tpl,
				autoHeight : true,
				emptyText : GO.calendar.lang.noParticipantsToDisplay,
				itemSelector : 'td.time',
				overClass : 'time-over'
			});

	dataView.on('click', function(dataview, index, node) {
				var time = node.id.substr(4);

				var colonIndex = time.indexOf(':');

				var minutes = time.substr(colonIndex + 1);
				var hours = time.substr(0, colonIndex);

				var frmStartHour = this.formPanel.form.findField('start_hour');
				var frmStartMin = this.formPanel.form.findField('start_min');
				var frmStartDate = this.formPanel.form.findField('start_date');

				var frmEndHour = this.formPanel.form.findField('end_hour');
				var frmEndMin = this.formPanel.form.findField('end_min');
				var frmEndDate = this.formPanel.form.findField('end_date');

				var hourDiff = parseInt(frmEndHour.getValue())
						- parseInt(frmStartHour.getValue());
				var minDiff = parseInt(frmEndMin.getValue())
						- parseInt(frmStartMin.getValue());

				if (minDiff < 0) {
					minDiff += 60;
					hourDiff--;
				}

				if (minutes < 10) {
					minutes = '0' + minutes;
				}

				alert(minutes);

				frmStartHour.setValue(hours);
				frmStartMin.setValue(minutes);
				frmStartDate.setValue(Date.parseDate(
						this.availabilityStore.baseParams.date,
						GO.settings.date_format));

				var endHour = parseInt(hours) + hourDiff;
				var endMin = parseInt(minutes) + minDiff;
				if (endMin > 60) {
					endMin -= 60;
					endHour++;
				}
				if (endMin < 10) {
					endMin = "0" + endMin;
				}

				frmEndHour.setValue(endHour);
				frmEndMin.setValue(endMin);
				frmEndDate.setValue(Date.parseDate(
						this.availabilityStore.baseParams.date,
						GO.settings.date_format));

				this.tabPanel.setActiveTab(0);
				this.availabilityWindow.hide();
			}, this);

	this.dataView.store.on('load', function() {
				Ext.get("availability_date")
						.update(this.availabilityStore.baseParams.date);
			}, this);

	Ext.apply(config, {
				layout : 'fit',
				modal : false,
				height : 400,
				width : 800,
				closeAction : 'hide',
				title : GO.lang.strAvailability,
				items : {
					layout : 'fit',
					cls : 'go-form-panel',
					waitMsgTarget : true,
					items : dataView,
					autoScroll : true
				},
				tbar : [{
					iconCls : 'btn-left-arrow',
					text : GO.calendar.lang.previousDay,
					cls : 'x-btn-text-icon',
					handler : function() {
						var date = Date.parseDate(
								this.dataView.store.baseParams.date,
								GO.settings.date_format).add(Date.DAY, -1);
						this.dataView.store.baseParams.date = date
								.format(GO.settings.date_format);
						this.dataView.store.load();
					},
					scope : this
				}, {
					iconCls : 'btn-right-arrow',
					text : GO.calendar.lang.nextDay,
					cls : 'x-btn-text-icon',
					handler : function() {
						var date = Date.parseDate(
								this.dataView.store.baseParams.date,
								GO.settings.date_format).add(Date.DAY, 1);
						this.dataView.store.baseParams.date = date
								.format(GO.settings.date_format);
						this.dataView.store.load();
					},
					scope : this
				}],
				buttons : [{
							text : GO.lang.cmdClose,
							handler : function() {
								this.hide();
							},
							scope : this
						}]
			});

	GO.calendar.AvailabilityWindow.superclass.call(this, config);
}

Ext.extend(GO.calendar.AvailabilityWindow, GO.Window, {

		show : function(config){
			this.dataView.store.baseParams.date=config.date;
			this.dataView.store.baseParams.event_id=config.event_id;
			this.dataView.store.baseParams.emails=config.email;
			this.dataView.store.load();
			
			GO.calendar.AvailabilityWindow.superclass.show.call(this);
		}

});